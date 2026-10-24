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


def _infer_dummy(features: np.ndarray) -> np.ndarray:
    n = len(get_classes())
    return np.random.dirichlet(np.ones(n) * 0.5)   # lebih peaked dari uniform


def _scale(features: np.ndarray) -> np.ndarray:
    scaler = get_model_bundle().get("scaler")
    if scaler is not None:
        return scaler.transform(features.reshape(1, -1))[0]
    return features


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
}

MECHANIC_NOTES_TEMPLATE = {
    "normal":             "Audio menunjukkan pola idle normal. Tidak diperlukan intervensi mekanis. Disarankan tetap dilakukan pemeriksaan visual rutin.",
    "timing_chain_belt":  "Indikasi wear pada komponen timing. Rekomendasikan inspeksi visual chain/belt tension, kondisi sprocket, dan oil seal. Pertimbangkan penggantian preventif jika > 80.000 km.",
    "valve_lifter":       "Indikasi valve train noise. Rekomendasikan pemeriksaan clearance katup, kondisi camshaft, dan hydraulic lifter. Cek tekanan oli untuk menyingkirkan oil starvation.",
    "rod_knock":          "HIGH PRIORITY. Indikasi kerusakan bantalan connecting rod. Rekomendasikan pengukuran tekanan oli dan teardown partial untuk inspeksi bearing journal. Kemungkinan rebuild atau penggantian engine.",
    "combustion_misfire": "Indikasi misfire. Rekomendasikan OBD scan untuk kode P030x. Periksa spark plug, coil, injector, dan kompresi silinder secara individual.",
    "exhaust_leak":       "Indikasi exhaust leak. Inspeksi visual pada manifold gasket, flex pipe, dan sambungan flange. Lakukan smoke test atau soapy water test untuk lokasi kebocoran.",
    "bearing_alternator": "Indikasi bearing noise pada aksesori belt drive. Lepas belt dan putar masing-masing pulley secara manual untuk isolasi sumber suara (alternator, AC compressor, idler, tensioner).",
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

    # Feature selection
    raw_features = features_dict["mfcc"]
    scaled       = _scale(raw_features)

    # Inference
    t0 = time.perf_counter()
    if backend == "onnx":
        probs = _infer_onnx(model, scaled)
    elif backend == "pytorch":
        probs = _infer_pytorch(model, scaled)
    elif backend == "keras":
        probs = _infer_keras(model, scaled)
    else:
        probs = _infer_dummy(scaled)
    inference_ms = round((time.perf_counter() - t0) * 1000, 2)

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
