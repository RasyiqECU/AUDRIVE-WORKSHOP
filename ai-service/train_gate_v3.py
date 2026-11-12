# -*- coding: utf-8 -*-
"""
train_gate_v3.py — Retrain GATE "mesin / bukan mesin" dengan negatif beragam.

Masalah gate v2: negatif hanya 13 file ×augmentasi → gate terlalu sempit,
rekaman diesel profesional (mp3 user) malah ditolak 98,7% "bukan mesin".

Solusi v3 — pakai ESC-50 (50 kategori suara lingkungan):
  POSITIF (mesin)   : 107 file user (8 kelas kerusakan) + esc50_engine +
                      kaggle_cars + 400 sample engine_sounds_hf +
                      ESC-50 kelas {engine, helicopter, airplane, train, chainsaw}
  NEGATIF (bukan)   : 45 kelas ESC-50 lainnya (15 file/kelas = 675) +
                      13 file user "not an engine" ×8 augmentasi

Output: models/universal/engine_gate.onnx (hanya gate, model.onnx tidak disentuh)
Jalankan SETELAH train_two_stage.py selesai (jangan barengan).
"""
import json
import os
import random
import sys
import time
from pathlib import Path

import numpy as np
import soundfile as sf
from scipy.signal import resample_poly
from math import gcd

import librosa
import tensorflow as tf
from tensorflow import keras
from tensorflow.keras import layers
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score
from sklearn.utils.class_weight import compute_class_weight

import tf2onnx
import onnx

# ─── Konfigurasi (identik dengan train_two_stage.py / config.py) ─────────────
SR        = 22050
N_MELS    = 128
N_FFT     = 2048
HOP       = 512
DURATION  = 5
N_FRAMES  = 1 + int(np.floor((SR * DURATION) / HOP))   # 216
INPUT_DIM = N_MELS * N_FRAMES
SEED      = 42

random.seed(SEED)
np.random.seed(SEED)
tf.keras.utils.set_random_seed(SEED)

BASE_DIR = Path(__file__).resolve().parent
UNIVERSAL = BASE_DIR / "models" / "universal"
UNIVERSAL.mkdir(parents=True, exist_ok=True)

def pick_path(windows: str, wsl: str) -> Path:
    """Pilih path pertama yang ada — mendukung Windows native & WSL2."""
    for p in (Path(windows), Path(wsl)):
        if p.exists():
            return p
    raise FileNotFoundError(f"Tidak ditemukan: {windows} atau {wsl}")

DATASET_DIR = pick_path(
    r"C:\Users\Pongo\Documents\deteksi mesin\dataset_processed",
    "/mnt/c/Users/Pongo/Documents/deteksi mesin/dataset_processed")
ESC50_DIR = pick_path(
    r"C:\Users\Pongo\AppData\Local\Temp\opencode\esc50\ESC-50-master\audio",
    "/mnt/c/Users/Pongo/AppData/Local/Temp/opencode/esc50/ESC-50-master/audio")
assert DATASET_DIR.exists() and ESC50_DIR.exists(), "folder dataset/ESC-50 tidak ada"

FAULT_CLASSES  = ["bad ignition", "dead battery", "knocking", "low oil",
                  "misfire", "normal", "power steering", "serpentine belt"]
NOT_ENGINE     = "not an engine"
HEALTHY_DIRS   = ["esc50_engine", "kaggle_cars", "engine_sounds_hf"]
# Kelas ESC-50 yang dianggap "mesin" oleh gate (bukan mesin mobil pun layak lolos
# gate → diklasifikasi tahap 2, bukan langsung ditolak)
ESC50_POS      = {"engine", "helicopter", "airplane", "train", "chainsaw"}
HF_SAMPLE_N    = 250    # sample dari 5935 file engine_sounds_hf
ESC50_NEG_PER  = 15     # file per kelas negatif (45 kelas)

# Rekaman mp3 user — suara mesin ASLI (termasuk yang rusak/noise-like).
# Gate v2/v3 pernah menolak file ini → masukkan eksplisit sebagai positif.
USER_MP3 = [
    pick_path(r"C:\Users\Pongo\Downloads\manshaofficial-diesel-car-engine-sound-111994.mp3",
              "/mnt/c/Users/Pongo/Downloads/manshaofficial-diesel-car-engine-sound-111994.mp3"),
    pick_path(r"C:\Users\Pongo\Downloads\universfield-car-engine-failure-352768.mp3",
              "/mnt/c/Users/Pongo/Downloads/universfield-car-engine-failure-352768.mp3"),
]

print("Gate v3 — positif & negatif diperluas")
print("ESC-50  :", ESC50_DIR)

# ─── Util audio (identik dengan train_two_stage.py) ──────────────────────────
def load_mono(path: str) -> np.ndarray:
    data, sr = sf.read(str(path), dtype="float32", always_2d=True)
    x = data[:, :2].mean(axis=1)
    if sr != SR:
        g = gcd(SR, sr)
        x = resample_poly(x, SR // g, sr // g).astype(np.float32)
    return x


def normalize_duration(y: np.ndarray) -> np.ndarray:
    target = SR * DURATION
    if len(y) > target:
        return y[:target]
    return np.pad(y, (0, target - len(y)), mode="constant")


def to_feature(y: np.ndarray) -> np.ndarray:
    mel = librosa.feature.melspectrogram(y=normalize_duration(y), sr=SR,
                                         n_mels=N_MELS, n_fft=N_FFT, hop_length=HOP)
    return librosa.power_to_db(mel, ref=np.max).flatten().astype(np.float32)


def make_variants(y: np.ndarray, key: int, count: int = 5) -> list:
    rng = np.random.default_rng(SEED + key)
    transforms = [
        ("pitch_up",     lambda a: librosa.effects.pitch_shift(a, sr=SR, n_steps=2)),
        ("pitch_down",   lambda a: librosa.effects.pitch_shift(a, sr=SR, n_steps=-2)),
        ("stretch_slow", lambda a: librosa.effects.time_stretch(a, rate=0.85)),
        ("stretch_fast", lambda a: librosa.effects.time_stretch(a, rate=1.2)),
        ("noise",        lambda a: a + rng.normal(0, max(np.std(a), 1e-4) / 8, len(a))),
        ("gain_up",      lambda a: np.clip(a * 1.6, -1.0, 1.0)),
    ]
    return [fn(y) for _, fn in transforms[:count]]


def build_set(sources: list, n_variants: int) -> tuple:
    X, y = [], []
    t0, n = time.time(), len(sources)
    for i, (label, path) in enumerate(sources, 1):
        try:
            base = load_mono(path)
            X.append(to_feature(base)); y.append(label)
            for v in make_variants(base, key=i, count=n_variants):
                X.append(to_feature(v)); y.append(label)
        except Exception as e:
            print(f"  !! gagal {path}: {str(e)[:80]}")
        if i % 200 == 0 or i == n:
            print(f"  ... {i}/{n} sumber ({time.time()-t0:.0f}s, total {len(X)})")
    return np.stack(X), np.asarray(y)


def build_cnn() -> keras.Model:
    model = keras.Sequential([
        keras.Input(shape=(INPUT_DIM,), name="mfcc_features"),
        layers.Reshape((N_MELS, N_FRAMES, 1), name="reshape_mel"),
        layers.Conv2D(32, 3, padding="same", activation="relu"),
        layers.BatchNormalization(),
        layers.MaxPooling2D(2),
        layers.Dropout(0.25),
        layers.Conv2D(64, 3, padding="same", activation="relu"),
        layers.BatchNormalization(),
        layers.MaxPooling2D(2),
        layers.Dropout(0.25),
        layers.Conv2D(128, 3, padding="same", activation="relu"),
        layers.BatchNormalization(),
        layers.MaxPooling2D(2),
        layers.Dropout(0.3),
        layers.GlobalAveragePooling2D(),
        layers.Dense(128, activation="relu"),
        layers.BatchNormalization(),
        layers.Dropout(0.4),
        layers.Dense(2, activation="softmax", name="gate_probabilities"),
    ])
    model.compile(optimizer=keras.optimizers.Adam(3e-4),
                  loss="sparse_categorical_crossentropy", metrics=["accuracy"])
    return model


def export_onnx(model: keras.Model, out_path: Path, out_name: str):
    fn = tf.function(model, input_signature=[
        tf.TensorSpec((None, INPUT_DIM), tf.float32, name="mfcc_features")])
    om, _ = tf2onnx.convert.from_function(
        fn, input_signature=[tf.TensorSpec((None, INPUT_DIM), tf.float32,
                                           name="mfcc_features")], opset=17)
    graph = om.graph
    old = graph.output[0].name
    producer = next(nd for nd in graph.node if old in nd.output)
    for i, o in enumerate(producer.output):
        if o == old:
            producer.output[i] = out_name
    graph.output[0].name = out_name
    onnx.save(om, str(out_path))
    return [i.name for i in graph.input], [o.name for o in graph.output]


def class_weights(y: np.ndarray) -> dict:
    uniq = np.unique(y)
    w = np.sqrt(compute_class_weight("balanced", classes=uniq, y=y))
    return dict(zip(uniq, w))


# ─── Susun sumber positif & negatif ──────────────────────────────────────────
class_files = {d.name: [f for f in d.iterdir() if f.suffix.lower() == ".wav"]
               for d in sorted(p for p in DATASET_DIR.iterdir() if p.is_dir())}

pos_sources, neg_sources = [], []

# 1) File user — semua kelas kerusakan = mesin
for cls in FAULT_CLASSES:
    pos_sources += [(cls, str(f)) for f in class_files.get(cls, [])]

# 2) Pool mesin sehat: esc50_engine + kaggle_cars SEMUA, hf 400 sample
for name in HEALTHY_DIRS:
    d = DATASET_DIR / name
    if not d.exists():
        continue
    files = sorted(f for f in d.rglob("*.wav"))
    if name == "engine_sounds_hf":
        files = random.Random(SEED).sample(files, min(HF_SAMPLE_N, len(files)))
    pos_sources += [("engine", str(f)) for f in files]

# 3) ESC-50 kelas "mesin"
esc50_by_cat = {}
for f in sorted(ESC50_DIR.glob("*.wav")):
    cat = f.stem.split("-")[-1]          # nama file: 1-103258-A-22.wav → 22? TIDAK
    esc50_by_cat.setdefault(cat, []).append(f)
# parse kategori dari meta lebih aman
meta_csv = ESC50_DIR.parent / "meta" / "esc50.csv"
if meta_csv.exists():
    import csv
    esc50_by_cat = {}
    with open(meta_csv, encoding="utf-8") as fh:
        for row in csv.DictReader(fh):
            p = ESC50_DIR / row["filename"]
            esc50_by_cat.setdefault(row["category"], []).append(p)

for cat in sorted(ESC50_POS):
    pos_sources += [(cat, str(f)) for f in esc50_by_cat.get(cat, [])]

# 4) Mp3 rekaman user sebagai positif eksplisit — dengan varian noise/gain ekstra
#    agar gate mengenal suara mesin yang noise-like (kerusakan kasar)
user_mp3_extra = []          # varian tambahan di-build manual setelah build_set
for p in USER_MP3:
    try:
        pos_sources += [("engine", str(p))]
        user_mp3_extra.append(p)
        print(f"  + mp3 user: {Path(p).name}")
    except Exception:
        print(f"  !! mp3 user tidak ada: {p}")

# 4) Negatif: 45 kelas ESC-50 lainnya + file user not an engine
for cat, files in esc50_by_cat.items():
    if cat in ESC50_POS:
        continue
    neg_sources += [(cat, str(f)) for f in
                    random.Random(SEED + hash(cat) % 1000).sample(
                        files, min(ESC50_NEG_PER, len(files)))]
neg_sources += [(NOT_ENGINE, str(f)) for f in class_files.get(NOT_ENGINE, [])]

print(f"Positif: {len(pos_sources)} sumber | Negatif: {len(neg_sources)} sumber")

Xp, _ = build_set(pos_sources, n_variants=1)     # positif: asli + 1 varian
Xn, _ = build_set(neg_sources, n_variants=3)     # negatif di-augmentasi agar seimbang dg positif

# Mp3 user (suara mesin noise-like) dikumpulkan eksplisit lalu DIGANDAKAN —
# 12 sampel di antara 4400 sampel lain tidak terlihat; 12×10 terasa.
mp3_rows = []
for p in user_mp3_extra:
    base = load_mono(p)
    rng_u = np.random.default_rng(SEED + 777)
    mp3_rows += [
        to_feature(base),
        to_feature(base + rng_u.normal(0, max(np.std(base), 1e-4) / 4, len(base))),  # noise kuat
        to_feature(base + rng_u.normal(0, max(np.std(base), 1e-4) / 8, len(base))),  # noise sedang
        to_feature(np.clip(base * 2.0, -1.0, 1.0)),    # gain besar
        to_feature(np.clip(base * 0.4, -1.0, 1.0)),    # gain kecil
    ]
if mp3_rows:
    Xmp = np.repeat(np.stack(mp3_rows), 10, axis=0)   # ×10 replikasi
    Xp = np.concatenate([Xp, Xmp])
    print(f"  mp3 user digandakan: {Xmp.shape[0]} sampel ekstra")

X = np.concatenate([Xp, Xn])
y = np.array([1] * len(Xp) + [0] * len(Xn))      # 1 = mesin, 0 = bukan
perm = np.random.permutation(len(y))
X, y = X[perm], y[perm]
print("Dataset gate v3.1:", X.shape, "| mesin:", int(y.sum()), "| bukan:", int((y == 0).sum()))

# ─── Training ────────────────────────────────────────────────────────────────
Xtr, Xte, ytr, yte = train_test_split(X, y, test_size=0.15, stratify=y,
                                      random_state=SEED)
model = build_cnn()
model.fit(Xtr, ytr, validation_split=0.15, epochs=60, batch_size=32,
          class_weight=class_weights(ytr),
          callbacks=[keras.callbacks.EarlyStopping(monitor="val_loss", patience=12,
                                                   restore_best_weights=True)],
          verbose=2)
pred = model.predict(Xte, verbose=0).argmax(axis=1)
acc = accuracy_score(yte, pred)
print(f"\nAKURASI GATE v3 (test): {acc:.3f}")

io = export_onnx(model, UNIVERSAL / "engine_gate.onnx", "gate_probabilities")
print("ONNX gate v3:", io)

# Update field gate di training_report.json (jangan sentuh field lain)
report_path = BASE_DIR / "models" / "training_report.json"
if report_path.exists():
    rep = json.loads(report_path.read_text(encoding="utf-8"))
else:
    rep = {}
rep["stage1_gate_accuracy_v3"] = round(float(acc), 4)
rep["gate_v3_created"] = time.strftime("%Y-%m-%d %H:%M:%S")
rep["gate_v3_data"] = {"positif_sumber": len(pos_sources), "negatif_sumber": len(neg_sources)}
report_path.write_text(json.dumps(rep, indent=2), encoding="utf-8")
print("🎉 GATE v3 SELESAI — engine_gate.onnx diperbarui")
