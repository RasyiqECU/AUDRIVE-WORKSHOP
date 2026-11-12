"""
app/predictor.py
Langkah 3 — Engine prediksi + penyusunan laporan diagnostik lengkap.
Output mencakup: identifikasi kerusakan, analisis gelombang, urgensi, rekomendasi.
"""

import logging
import time
from typing import Any

import numpy as np

import config
from app.loader import get_model_bundle, get_classes

logger = logging.getLogger(__name__)


# ─── Inference Backends ───────────────────────────────────────────────────────

def _infer_onnx(model: Any, features: np.ndarray) -> np.ndarray:
    inp_name = model.get_inputs()[0].name
    out = model.run(None, {inp_name: features.astype(np.float32).reshape(1, -1)})
    return out[0][0]


def _infer_pytorch(model: Any, features: np.ndarray) -> np.ndarray:
    import torch
    with torch.no_grad():
        t   = torch.tensor(features, dtype=torch.float32).unsqueeze(0)
        out = model(t)
        return torch.softmax(out, dim=1).numpy()[0]


def _infer_keras(model: Any, features: np.ndarray) -> np.ndarray:
    return model.predict(features.astype(np.float32).reshape(1, -1), verbose=0)[0]


def _infer_xgboost(model: Any, features: np.ndarray) -> np.ndarray:
    probs = model.predict_proba(features.astype(np.float32).reshape(1, -1))[0]
    return np.asarray(probs, dtype=np.float64)


def _infer_dummy(features: np.ndarray) -> np.ndarray:
    n = len(get_classes())
    return np.random.dirichlet(np.ones(n) * 0.5)   # lebih peaked dari uniform


def _scale(features: np.ndarray) -> np.ndarray:
    scaler = get_model_bundle().get("scaler")
    if scaler is not None:
        return scaler.transform(features.reshape(1, -1))[0]
    return features


def _yamnet_embedding(bundle: dict, features_dict: dict) -> np.ndarray | None:
    """Waveform 16 kHz → embedding YAMNet (1024,). None jika frontend tidak aktif."""
    yam = bundle.get("yamnet")
    if not yam or not yam[0]:
        return None
    kind, obj = yam
    wave = features_dict.get("waveform_16k")
    if wave is None:
        logger.warning("waveform_16k tidak ada di features — pipeline YAMNet dilewati.")
        return None
    wave = np.asarray(wave, dtype=np.float32).reshape(-1)
    try:
        if kind == "onnx":
            # yamnet.onnx diekspor dengan TensorSpec [None] → input harus rank-1
            out = obj.run(None, {obj.get_inputs()[0].name: wave})[0]
            return np.asarray(out, dtype=np.float32).reshape(-1)
        # fallback TF-Hub runtime: module(wave) → (scores, embeddings, logmel)
        _, emb, _ = obj(wave)
        return emb.numpy().mean(axis=0).astype(np.float32)
    except Exception as e:
        logger.warning(f"YAMNet embedding gagal ({e}) — fallback pipeline lama.")
        return None


def _build_feature_vector(features_dict: dict) -> np.ndarray:
    """
    Susun vektor fitur mentah sesuai jumlah yang dibutuhkan scaler/model.

    Model XGBoost/StandardScaler milik AUDrive dilatih dengan 82 fitur
    (40 MFCC mean + 40 MFCC std + spectral_rolloff + zero_crossing_rate),
    sementara preprocessor menghasilkan 80 fitur inti — 2 fitur spektral
    ditambahkan di sini bila dimensi belum cukup.
    """
    raw_features = features_dict["mfcc"]

    scams = get_model_bundle().get("scaler")
    expected = getattr(scams, "n_features_in_", None)
    model = get_model_bundle().get("model")
    if expected is None and model is not None:
        expected = getattr(model, "n_features_in_", None)

    if expected is not None and len(raw_features) < expected:
        spectral = features_dict.get("spectral", {})
        extras = [spectral.get("spectral_rolloff_hz"),
                  spectral.get("zero_crossing_rate")]
        needed = expected - len(raw_features)
        extras = [0.0 if x is None else float(x) for x in extras[:needed]]
        raw_features = np.concatenate([raw_features, np.array(extras)])

    return raw_features


# ─── Langkah 3: Susun Laporan Diagnostik ────────────────────────────────────

# Nama ramah bahasa Indonesia
CLASS_LABELS = {
    "normal":             "Mesin Normal",
    "timing_chain_belt":  "Kerusakan Timing Chain / Belt",
    "valve_lifter":       "Kerusakan Katup / Lifter",
    "rod_knock":          "Rod Knock / Kerusakan Bantalan Mesin",
    "combustion_misfire": "Masalah Pembakaran / Misfire",
    "exhaust_leak":       "Kebocoran Knalpot / Exhaust Leak",
    "bearing_alternator": "Kerusakan Bearing Alternator / Pulley",
    # Kelas pelatihan model XGBoost (9 kelas)
    "bad_ignition":       "Sistem Pengapian Bermasalah (Bad Ignition)",
    "dead_battery":       "Aki Lemah / Mati (Dead Battery)",
    "low_oil":            "Level Oli Mesin Rendah",
    "normal_brakes":      "Rem Normal",
    "normal_engine_idle": "Mesin Normal (Idle Sehat)",
    "not an engine":      "Rekaman Bukan Suara Mesin",
    "power_steering":     "Masalah Power Steering",
    "serpentine_belt":    "Serpentine Belt Aus / Kendor",
    "worn_out_brakes":    "Lapisan Rem Aus (Worn-out Brakes)",
}

URGENCY_LABELS = {
    "safe":      {"label": "Aman / Tidak Perlu ke Bengkel",  "icon": "✅", "action": "Kondisi mesin terdengar normal. Lanjutkan servis rutin sesuai jadwal."},
    "monitor":   {"label": "Perlu Dipantau",                 "icon": "⚠️", "action": "Pantau kondisi dalam 1–2 minggu. Segera ke bengkel jika suara memburuk."},
    "urgent":    {"label": "Perlu Segera ke Bengkel",        "icon": "🔶", "action": "Jadwalkan kunjungan ke bengkel dalam 1–3 hari. Hindari perjalanan jauh."},
    "emergency": {"label": "DARURAT — Hentikan Kendaraan",   "icon": "🚨", "action": "Hentikan kendaraan dan jangan nyalakan mesin. Risiko kerusakan total sangat tinggi."},
}

WAVE_DESCRIPTIONS = {
    "normal":             "Pola gelombang stabil dan reguler. Frekuensi dominan dalam rentang normal idle mesin. Tidak terdeteksi anomali signifikan.",
    "timing_chain_belt":  "Terdeteksi suara gesekan/berisik di frekuensi menengah yang berulang secara ritmik, mengindikasikan komponen timing chain atau belt yang aus.",
    "valve_lifter":       "Terdeteksi pola ketukan tajam (*ticking*) berulang di frekuensi tinggi, khas pada area kepala silinder. Mengindikasikan kelonggaran pada katup atau lifter.",
    "rod_knock":          "Terdeteksi ketukan berat dan dalam (*knocking*) di frekuensi rendah dari blok mesin. Ini adalah tanda serius kerusakan pada bantalan connecting rod.",
    "combustion_misfire": "Terdeteksi pola getaran tidak konsisten dan suara pincang (*misfiring*). Siklus pembakaran tidak stabil, mengindikasikan misfire pada satu atau lebih silinder.",
    "exhaust_leak":       "Terdeteksi suara mendesis (*hissing*) atau letupan (*popping*) yang berulang, khas pada kebocoran sistem gas buang.",
    "bearing_alternator": "Terdeteksi suara melengking (*whining/squeal*) di frekuensi tinggi yang konstan, mengindikasikan bearing alternator atau pulley yang aus.",
    # Kelas pelatihan model XGBoost (9 kelas)
    "bad_ignition":       "Terdeteksi ketukan/letupan tidak normal saat mesin menyala (*backfire / crank sound*), mengindikasikan masalah pada sistem pengapian.",
    "dead_battery":       "Terdeteksi suara klik lemah berulang (*clicking*) saat start, ciri khas aki lemah atau sistem kelistrikan yang tidak cukup daya.",
    "low_oil":            "Terdeteksi gesekan logam dari bagian dalam blok mesin, indikasi pelumasan tidak memadai karena level oli rendah.",
    "normal_brakes":      "Pola gelombang saat pengereman stabil, tidak terdengar decitan (squeal) atau derit yang mengindikasikan aus.",
    "normal_engine_idle": "Pola gelombang stabil dan reguler pada idle mesin. Tidak terdeteksi anomali signifikan.",
    "not an engine":      "Suara pada rekaman tidak menyerupai karakteristik mesin kendaraan. Kemungkinan merekam lingkungan berisik atau sumber suara yang salah.",
    "power_steering":     "Terdeteksi suara whining/dengungan saat kemudi diputar, menandakan pompa power steering bermasalah atau fluida hydraulic-nya rendah.",
    "serpentine_belt":    "Terdeteksi decitan (*squealing*) di latar frekuensi tinggi, khas serpentine belt yang aus, kendur, atau licin.",
    "worn_out_brakes":    "Terdeteksi decitan/derit logam berulang saat rem ditekan, mengindikasikan lapisan brake pad sudah tipis/aus.",
}

RECOMMENDATIONS_CUSTOMER = {
    "normal": [
        "✅ Mesin Anda terdengar dalam kondisi sehat.",
        "Lanjutkan jadwal servis rutin (ganti oli, filter, dll.) sesuai rekomendasi pabrikan.",
        "Pantau terus kondisi mesin dan segera konsultasikan jika muncul suara baru.",
    ],
    "timing_chain_belt": [
        "Segera periksa kondisi timing chain / timing belt ke bengkel terpercaya.",
        "Hindari menunda — timing belt yang putus dapat merusak mesin secara total.",
        "Cek tegangan (tension) dan kondisi fisik belt/chain beserta komponen pully-nya.",
        "⚠️ Jangan lakukan perjalanan jauh sebelum diperiksa.",
    ],
    "valve_lifter": [
        "Periksa celah katup (valve clearance) dan kondisi rocker arm / lifter.",
        "Pastikan level dan kualitas oli mesin dalam kondisi baik.",
        "Bersihkan atau ganti hydraulic lifter jika diperlukan.",
        "Jadwalkan tune-up mesin di bengkel dalam waktu dekat.",
    ],
    "rod_knock": [
        "🚨 PERHATIAN SERIUS: Hentikan penggunaan kendaraan jika suara terasa semakin kuat.",
        "Segera bawa ke bengkel atau hubungi layanan derek.",
        "Rod knock dapat menyebabkan kerusakan total mesin jika dibiarkan.",
        "Jangan coba menambah RPM atau memaksakan mesin.",
    ],
    "combustion_misfire": [
        "Periksa dan ganti busi yang aus atau rusak.",
        "Cek kondisi coil ignition dan kabel busi.",
        "Periksa injektor bahan bakar — kemungkinan tersumbat.",
        "Baca kode error (ECU scan) untuk identifikasi silinder yang bermasalah.",
    ],
    "exhaust_leak": [
        "Periksa kondisi manifold knalpot dan gasket-nya.",
        "Cek sambungan pipa gas buang dari manifold hingga muffler.",
        "Kebocoran knalpot dapat menyebabkan gas CO masuk ke kabin — hindari AC recirculate.",
        "Tambalan sementara tersedia, namun penggantian komponen sangat disarankan.",
    ],
    "bearing_alternator": [
        "Periksa kondisi alternator, idler pulley, dan tensioner pulley.",
        "Cek apakah suara muncul saat AC dinyalakan atau load listrik tinggi.",
        "Ganti bearing alternator atau pulley yang aus.",
        "Pantau voltase aki — alternator yang rusak dapat menguras daya aki.",
    ],
    # Kelas pelatihan model XGBoost (9 kelas)
    "bad_ignition": [
        "Periksa kondisi busi, koil pengapian, dan kabel/konektornya.",
        "Cek pengapian (ignition timing) jika mesin sulit dinyalakan.",
        "Baca kode error ECU untuk mengetahui silinder atau komponen yang bermasalah.",
        "Hindari menyalakan mesin berulang-ulang sebelum diperiksa teknisi.",
    ],
    "dead_battery": [
        "Uji voltase dan kondisi aki (idealnya di atas 12,4 V saat mesin mati).",
        "Bersihkan terminal aki dari kerak/oksida dan pastikan pengencangnya kuat.",
        "Cek alternator dan sistem charging — aki lemah bisa karena tidak ter-charge.",
        "Sediakan jumper atau ganti aki jika sudah end-of-life.",
    ],
    "low_oil": [
        "Segera matikan mesin dan cek level oli dengan dipstick di tempat datar.",
        "Tambah oli sesuai spesifikasi pabrikan — jangan menunda.",
        "Cari kebocoran (rembesan di bawah mesin, seal, drain plug) penyebab oli berkurang.",
        "Jalankan mesin kembali hanya setelah level oli benar.",
    ],
    "normal_brakes": [
        "✅ Sistem rem terdengar normal.",
        "Lanjutkan pemeriksaan rem berkala (minyak rem, kampas, disc).",
        "Segera konsultasikan jika muncul suara baru atau pedal terasa berubah.",
    ],
    "normal_engine_idle": [
        "✅ Mesin Anda terdengar dalam kondisi sehat.",
        "Lanjutkan jadwal servis rutin (penggantian oli, filter, dll.) sesuai rekomendasi pabrikan.",
        "Pantau terus kondisi mesin dan segera konsultasikan jika muncul suara baru.",
    ],
    "not an engine": [
        "🎤 Rekam ulang dengan mikrofon diarahkan ke area mesin/roda (buka kap mesin).",
        "Pastikan mesin menyala saat merekam (idle, jarak 30–50 cm dari mesin).",
        "Rekam di tempat tenang, selama 5–10 detik, tanpa suara lingkungan lain.",
    ],
    "power_steering": [
        "Cek level fluida hydraulic power steering dan tambahkan sesuai spesifikasi.",
        "Periksa selang dan sambungan — suara whining sering muncul karena bocor atau air masuk.",
        "Periksa pompa power steering; ganti bearing/komponen yang aus.",
        "Jangan tahan kemudi di posisi lock berulang-ulang sebelum diperiksa.",
    ],
    "serpentine_belt": [
        "Periksa kondisi serpentine belt — cari retakan, kelistrikan licin, atau gigi yang hilang.",
        "Cek satu per satu komponen yang dibawanya (alternator, AC compressor, tensioner, idler).",
        "Ganti belt sesuai interval servis — belt putus bisa membuat mesin berhenti total.",
    ],
    "worn_out_brakes": [
        "🚨 Segera periksa brake pad dan rotor — keausan ini sangat berdampak pada keselamatan.",
        "Periksa level minyak rem dan kondisi caliper.",
        "Jangan tunda penggantian brake pad — berhenti di tempat aman jika suara memburuk.",
    ],
}

MECHANIC_NOTES_TEMPLATE = {
    "normal":             "Audio menunjukkan pola idle normal. Tidak diperlukan intervensi mekanis. Disarankan tetap dilakukan pemeriksaan visual rutin.",
    "timing_chain_belt":  "Indikasi wear pada komponen timing. Rekomendasikan inspeksi visual chain/belt tension, kondisi sprocket, dan oil seal. Pertimbangkan penggantian preventif jika > 80.000 km.",
    "valve_lifter":       "Indikasi valve train noise. Rekomendasikan pemeriksaan clearance katup, kondisi camshaft, dan hydraulic lifter. Cek tekanan oli untuk menyingkirkan oil starvation.",
    "rod_knock":          "HIGH PRIORITY. Indikasi kerusakan bantalan connecting rod. Rekomendasikan pengukuran tekanan oli dan teardown partial untuk inspeksi bearing journal. Kemungkinan rebuild atau penggantian engine.",
    "combustion_misfire": "Indikasi misfire. Rekomendasikan OBD scan untuk kode P030x. Periksa spark plug, coil, injector, dan kompresi silinder secara individual.",
    "exhaust_leak":       "Indikasi exhaust leak. Inspeksi visual pada manifold gasket, flex pipe, dan sambungan flange. Lakukan smoke test atau soapy water test untuk lokasi kebocoran.",
    "bearing_alternator": "Indikasi bearing noise pada aksesori belt drive. Lepas belt dan putar masing-masing pulley secara manual untuk isolasi sumber suara (alternator, AC compressor, idler, tensioner).",
    # Kelas pelatihan model XGBoost (9 kelas)
    "bad_ignition":       "Indikasi masalah sistem pengapian (spark plug / koil / distributor). Rekomendasikan inspeksi visual busi & koil, resistance test koil, dan OBD scan untuk kode P0300-P0308.",
    "dead_battery":       "Indikasi aki lemah / charging system tidak optimum. Ukur voltase aki off & on (mesin), periksa alternator output (14,0-14,4 V), dan load test aki.",
    "low_oil":            "Indikasi level oli rendah / pelumasan tidak memadai. Cek level dengan dipstick, telusuri sumber kebocoran, ganti oli + filter, dan periksa keausan bearing/cam jika sudah lama diabaikan.",
    "normal_brakes":      "Rekaman pengereman terdengar normal. Pantau suara pad & rotor secara berkala; tidak perlu aksi perbaikan saat ini.",
    "normal_engine_idle": "Audio menunjukkan pola idle normal. Tidak diperlukan intervensi mekanis. Disarankan tetap dilakukan pemeriksaan visual rutin.",
    "not an engine":      "Rekaman tidak mengandung suara mesin — kemungkinan hanya suara lingkungan. Minta customer merekam ulang sesuai panduan untuk dianalisis kembali.",
    "power_steering":     "Indikasi noise pada sistem power steering. Cek level & kondisi fluida hydraulic, selang (bocor/kerut), dan pompa. Lakukan uji suara pompa saat kemudi dipenuh untuk isolasi sumber.",
    "serpentine_belt":    "Indikasi belt aus / tensioner lemah. Inspeksi visual belt (crack, glazing), ukur deflection belt, dan periksa tensioner & bearing idler pulley.",
    "worn_out_brakes":    "HIGH PRIORITY (keselamatan). Indikasi lapisan brake pad tipis. Ukur ketebalan pad & rotor (biasanya pad < 3 mm wajib ganti), periksa caliper dan level minyak rem.",
}


# ─── Fungsi Utama Predict ─────────────────────────────────────────────────────

def predict(features_dict: dict, quality_info: dict) -> dict:
    """
    Langkah 2 & 3:
    Input : fitur dari preprocessor + info kualitas audio
    Output: laporan diagnostik lengkap sesuai format workflow
    """
    bundle  = get_model_bundle()
    model   = bundle["model"]
    backend = bundle["backend"]
    classes = get_classes()

    # Feature selection — susun sesuai dimensi yang diharapkan model
    mel_features = None
    if backend == "onnx":
        # Model CNN Mel-Spectrogram menerima Log-Mel yang di-flatten (27.648 fitur);
        # model head YAMNet menerima embedding 1024 — frontend yamnet.onnx dipakai
        # jika tersedia, fallback ke pipeline lama bila tidak.
        mel_features = np.asarray(features_dict["mel_spectrogram"]).flatten()
        embedding    = _yamnet_embedding(bundle, features_dict)
        raw_features = embedding if embedding is not None else mel_features
    else:
        raw_features = _build_feature_vector(features_dict)
    scaled       = _scale(raw_features)

    # Inference
    t0 = time.perf_counter()
    if backend == "xgboost":
        probs = _infer_xgboost(model, scaled)
    elif backend == "onnx":
        probs = _infer_onnx(model, scaled)
    elif backend == "pytorch":
        probs = _infer_pytorch(model, scaled)
    elif backend == "keras":
        probs = _infer_keras(model, scaled)
    else:
        probs = _infer_dummy(scaled)
    inference_ms = round((time.perf_counter() - t0) * 1000, 2)

    # ── Tahap 1: Gate "mesin / bukan mesin" (hanya backend onnx, jika tersedia) ──
    # Mencegah suara mesin asli salah dilabel 'not an engine', dan memfilter
    # rekaman yang benar-benar bukan suara mesin sebelum klasifikasi kerusakan.
    probs_stage2 = probs.copy()
    gate = bundle.get("gate")
    if backend == "onnx" and gate is not None:
        try:
            gname  = gate.get_inputs()[0].name
            # Gate dilatih pada Mel 27648 — selalu pakai mel, bukan embedding
            gate_in = mel_features if mel_features is not None else scaled
            gout    = gate.run(None, {gname: gate_in.astype(np.float32).reshape(1, -1)})[0][0]
            p_none = float(gout[0])   # probabilitas "bukan mesin"
            if "not an engine" in classes:
                idx_ne = classes.index("not an engine")
                others = probs_stage2.copy()
                others[idx_ne] = 0.0
                if others.sum() > 0:
                    others = others / others.sum()
                if p_none > (1.0 - config.GATE_THRESHOLD):
                    # Gate yakin BUKAN mesin → paksa kelas 'not an engine'
                    probs = others * (1.0 - p_none)
                    probs[idx_ne] = p_none
                elif probs_stage2[idx_ne] >= config.GATE_THRESHOLD:
                    # Head sendiri yakin bukan mesin (≥ threshold) → pertahankan
                    # 'not an engine'. JANGAN dimask: masking saat head yakin
                    # hanya memaksa fault palsu pada suara non-mesin yang lolos gate.
                    probs = probs_stage2
                else:
                    # Gate lolos & head tidak yakin → mask 'not an engine'
                    probs = others
        except Exception as e:
            logger.warning(f"Gate gagal dijalankan ({e}) — lanjut tanpa gate.")

    # Normalisasi ke probabilitas
    if probs.max() > 1.0:
        probs = np.exp(probs) / np.exp(probs).sum()

    # Top prediction
    top_idx    = int(np.argmax(probs))
    top_class  = classes[top_idx] if top_idx < len(classes) else "normal"
    confidence = float(probs[top_idx])

    # Top-3
    sorted_idx = np.argsort(probs)[::-1][:3]
    top3 = [
        {"class": classes[i] if i < len(classes) else "?",
         "label": CLASS_LABELS.get(classes[i] if i < len(classes) else "?", classes[i] if i < len(classes) else "?"),
         "probability": round(float(probs[i]), 4)}
        for i in sorted_idx
    ]

    # Urgency
    urgency_level = config.URGENCY_MAP.get(top_class, "monitor")
    urgency_meta  = URGENCY_LABELS[urgency_level]

    # Wave analysis (dari spectral features)
    spectral = features_dict.get("spectral", {})
    anomaly  = top_class != "normal"
    wave_analysis = {
        "dominant_frequency_hz": spectral.get("dominant_frequency_hz"),
        "pattern_description":   WAVE_DESCRIPTIONS.get(top_class, "Pola tidak dikenali."),
        "anomaly_detected":      anomaly,
        "anomaly_details":       _build_anomaly_detail(top_class, spectral, confidence) if anomaly else None,
    }

    # Apakah perlu manual review?
    needs_review = (
        confidence < config.CONFIDENCE_THRESHOLD
        or quality_info.get("quality_score", 1.0) < config.QUALITY_THRESHOLD + 0.2
        or backend == "dummy"
    )

    return {
        "predicted_class":    top_class,
        "class_label":        CLASS_LABELS.get(top_class, top_class),
        "confidence":         round(confidence, 4),
        "top3":               top3,
        "wave_analysis":      wave_analysis,
        "urgency": {
            "level":  urgency_level,
            "label":  urgency_meta["label"],
            "action": urgency_meta["action"],
            "icon":   urgency_meta["icon"],
        },
        "recommendations":    RECOMMENDATIONS_CUSTOMER.get(top_class, []),
        "mechanic_notes":     MECHANIC_NOTES_TEMPLATE.get(top_class, "Lakukan pemeriksaan menyeluruh."),
        "spectrogram_path":   features_dict.get("spectrogram_path"),
        "audio_quality":      quality_info,
        "inference_ms":       inference_ms,
        "backend":            backend,
        "is_dummy":           backend == "dummy",
        "needs_manual_review": needs_review,
    }


def _build_anomaly_detail(cls: str, spectral: dict, confidence: float) -> str:
    """Kalimat deskriptif singkat tentang anomali yang terdeteksi."""
    freq = spectral.get("dominant_frequency_hz", 0)
    zcr  = spectral.get("zero_crossing_rate", 0)
    onset = spectral.get("onset_strength", 0)

    details = []
    if freq:
        details.append(f"Frekuensi dominan: {freq:.0f} Hz")
    if zcr > 0.1:
        details.append("tingkat penyilangan nol tinggi (suara tajam/berisik)")
    if onset > 1.5:
        details.append("onset kuat terdeteksi (pola ketukan/impulsif)")
    details.append(f"Tingkat kepercayaan AI: {confidence:.0%}")

    return " | ".join(details) if details else None
