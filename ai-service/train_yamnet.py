# -*- coding: utf-8 -*-
"""
train_yamnet.py — Klasifikasi kerusakan mesin via TRANSFER LEARNING YAMNet.

Latar belakang: CNN from-scratch gagal generalisasi (val collapse) karena data
hanya ±96 rekaman unik — model menghafal "sidik jari rekaman", bukan ciri
kerusakan. YAMNet = model audio Google pretrained pada jutaan klip AudioSet
(termasuk ribuan suara mesin) → embedding 1024-d yang bermakna meski data kecil.

Pipeline baru:
  audio 16kHz → yamnet.onnx → embedding 1024 → head.onnx → 9 kelas
  (gate mel 27648 TIDAK diubah — tetap engine_gate.onnx lama)

Output:
  models/universal/model.onnx        (head, input yamnet_embedding 1024)
  models/universal/yamnet.onnx       (frontend embedding, input waveform 16k)
  models/yamnet_tfhub/               (salinan SavedModel utk fallback runtime)
  labels.json / label_encoder.pkl / training_report.json
Jalankan: wsl_run.sh train_yamnet.py   (±5 menit di GPU)
"""
import json
import os
import random
import shutil
import time
from math import gcd
from pathlib import Path

import numpy as np
import soundfile as sf
from scipy.signal import resample_poly

import tensorflow as tf
import tensorflow_hub as hub
from tensorflow import keras
from tensorflow.keras import layers
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report, accuracy_score
from sklearn.utils.class_weight import compute_class_weight
from sklearn.preprocessing import LabelEncoder

import tf2onnx
import onnx

SR_Y = 16000                    # sample rate YAMNet
DUR = 5
LEN_Y = SR_Y * DUR              # 80000 sampel
SEED = 42
random.seed(SEED); np.random.seed(SEED); tf.keras.utils.set_random_seed(SEED)

BASE_DIR = Path(__file__).resolve().parent
MODELS_DIR = BASE_DIR / "models"
UNIVERSAL = MODELS_DIR / "universal"
PREPROC = MODELS_DIR / "preprocessing"
UNIVERSAL.mkdir(parents=True, exist_ok=True)

def pick_path(windows: str, wsl: str) -> Path:
    for p in (Path(windows), Path(wsl)):
        if p.exists():
            return p
    raise FileNotFoundError(f"Tidak ditemukan: {windows} atau {wsl}")

DATASET_DIR = pick_path(
    r"C:\Users\Pongo\Documents\deteksi mesin\dataset_processed",
    "/mnt/c/Users/Pongo/Documents/deteksi mesin/dataset_processed")

FAULT_CLASSES = ["bad ignition", "dead battery", "knocking", "low oil",
                 "misfire", "normal", "power steering", "serpentine belt"]
NOT_ENGINE = "not an engine"
YAMNET_URL = "https://tfhub.dev/google/yamnet/1"

# ─── Load YAMNet ─────────────────────────────────────────────────────────────
print("Memuat YAMNet dari TF Hub ...")
yamnet = hub.load(YAMNET_URL)
print("YAMNet OK")

# ─── Util audio ──────────────────────────────────────────────────────────────
def load_mono16k(path) -> np.ndarray:
    """Baca file → mono → 16 kHz → tepat 5 detik (80000 sampel)."""
    data, sr = sf.read(str(path), dtype="float32", always_2d=True)
    x = data[:, :min(2, data.shape[1])].mean(axis=1)
    if sr != SR_Y:
        g = gcd(SR_Y, sr)
        x = resample_poly(x, SR_Y // g, sr // g).astype(np.float32)
    return x[:LEN_Y] if len(x) > LEN_Y else np.pad(x, (0, LEN_Y - len(x)))


def make_variants(y: np.ndarray, key: int) -> list:
    """5 varian AMAN mesin: noise ×2, gain ×2, rotasi waktu (tanpa pitch/stretch)."""
    rng = np.random.default_rng(SEED + key)
    return [
        y + rng.normal(0, max(np.std(y), 1e-4) / 8, len(y)),
        y + rng.normal(0, max(np.std(y), 1e-4) / 4, len(y)),
        np.clip(y * 1.5, -1.0, 1.0),
        y * 0.5,
        np.roll(y, int(rng.integers(1, len(y)))),
    ]


def embed(wave: np.ndarray) -> np.ndarray:
    """Waveform 16k → embedding YAMNet rata-rata (1024,)."""
    scores, emb, _ = yamnet(wave.astype(np.float32))
    return emb.numpy().mean(axis=0).astype(np.float32)


def build_set(sources: list) -> tuple:
    """[(label, path)] → (X embeddings, y label), tiap sumber = asli + 5 varian."""
    X, y = [], []
    t0, n = time.time(), len(sources)
    for i, (label, path) in enumerate(sources, 1):
        try:
            base = load_mono16k(path)
            X.append(embed(base)); y.append(label)
            for v in make_variants(base, key=i):
                X.append(embed(v)); y.append(label)
        except Exception as e:
            print(f"  !! gagal {path}: {str(e)[:70]}")
        if i % 100 == 0 or i == n:
            print(f"  ... {i}/{n} sumber ({time.time()-t0:.0f}s, {len(X)} sampel)")
    return np.stack(X), np.asarray(y)


# ─── Susun sumber ────────────────────────────────────────────────────────────
class_files = {d.name: [f for f in d.iterdir() if f.suffix.lower() == ".wav"]
               for d in sorted(p for p in DATASET_DIR.iterdir() if p.is_dir())}

rng = random.Random(SEED)
sources = []
for cls in FAULT_CLASSES:
    sources += [(cls, str(f)) for f in class_files.get(cls, [])]

# Healthy pool moderat agar kelas 'normal' tidak mendominasi
healthy = []
for name, cap in [("esc50_engine", 40), ("kaggle_cars", 60), ("engine_sounds_hf", 60)]:
    d = DATASET_DIR / name
    if d.exists():
        files = sorted(f for f in d.rglob("*.wav"))
        healthy += [str(f) for f in rng.sample(files, min(cap, len(files)))]
sources += [("normal", p) for p in healthy]
sources += [(NOT_ENGINE, str(f)) for f in class_files.get(NOT_ENGINE, [])]

print(f"Total sumber: {len(sources)}")

src_labels = [s[0] for s in sources]
idx_train_s, idx_tmp_s = train_test_split(range(len(sources)), test_size=0.30,
                                          stratify=src_labels, random_state=SEED)
idx_val_s, idx_test_s = train_test_split(idx_tmp_s, test_size=0.50,
                                         stratify=[src_labels[i] for i in idx_tmp_s],
                                         random_state=SEED)

def expand(idxs):
    out = []
    for i in idxs:
        out.extend(range(i * 6, i * 6 + 6))
    return np.array(out)

print("Ekstraksi embedding YAMNet ...")
X_all, y_all = build_set(sources)
print("Dataset:", X_all.shape)

tr, va, te = expand(idx_train_s), expand(idx_val_s), expand(idx_test_s)
Xtr, ytr = X_all[tr], y_all[tr]
Xva, yva = X_all[va], y_all[va]
Xte, yte = X_all[te], y_all[te]
print(f"Train {len(tr)} | Val {len(va)} | Test {len(te)}")

le = LabelEncoder().fit(sorted(set(y_all)))
def enc(y):
    return le.transform(y)
classes = list(le.classes_)

# ─── Head kecil di atas embedding ────────────────────────────────────────────
def build_head(num_classes: int) -> keras.Model:
    model = keras.Sequential([
        keras.Input(shape=(1024,), name="yamnet_embedding"),
        layers.BatchNormalization(name="bn_in"),
        layers.Dense(128, activation="relu", name="d1"),
        layers.Dropout(0.4),
        layers.Dense(64, activation="relu", name="d2"),
        layers.Dropout(0.4),
        layers.Dense(num_classes, activation="softmax", name="class_probabilities"),
    ])
    model.compile(optimizer=keras.optimizers.Adam(1e-3),
                  loss="sparse_categorical_crossentropy", metrics=["accuracy"])
    return model

uniq = np.unique(ytr)
w = np.sqrt(compute_class_weight("balanced", classes=uniq, y=ytr))
model = build_head(len(classes))
model.fit(Xtr, enc(ytr), validation_data=(Xva, enc(yva)),
          epochs=300, batch_size=32, class_weight=dict(zip(uniq, w)),
          callbacks=[
              keras.callbacks.EarlyStopping(monitor="val_accuracy", mode="max",
                                            patience=30, restore_best_weights=True),
              keras.callbacks.ReduceLROnPlateau(monitor="val_loss", factor=0.5,
                                                patience=8, min_lr=1e-5, verbose=1),
          ],
          verbose=2)

pred = model.predict(Xte, verbose=0).argmax(axis=1)
acc = accuracy_score(enc(yte), pred)
print(f"\nAKURASI YAMNET (test): {acc:.3f}")
print(classification_report(enc(yte), pred, target_names=classes, zero_division=0))

# ─── Export head → ONNX ──────────────────────────────────────────────────────
def export_onnx(kmodel, out_paths: list, in_name: str, out_name: str):
    fn = tf.function(kmodel, input_signature=[tf.TensorSpec((None, 1024), tf.float32, name=in_name)])
    om, _ = tf2onnx.convert.from_function(
        fn, input_signature=[tf.TensorSpec((None, 1024), tf.float32, name=in_name)], opset=17)
    g = om.graph
    old = g.output[0].name
    producer = next(nd for nd in g.node if old in nd.output)
    for i, o in enumerate(producer.output):
        if o == old:
            producer.output[i] = out_name
    g.output[0].name = out_name
    for p in out_paths:
        onnx.save(om, str(p))
    return [i.name for i in g.input], [o.name for o in g.output]

io_head = export_onnx(model, [UNIVERSAL / "model.onnx",
                              MODELS_DIR / "engine_fault_detector.onnx"],
                      "yamnet_embedding", "class_probabilities")
print("ONNX head:", io_head)

# ─── Export YAMNet frontend → ONNX (input waveform, output embedding) ───────
class YamnetMean(tf.Module):
    def __init__(self, m):
        super().__init__()
        self.m = m

    @tf.function(input_signature=[tf.TensorSpec([None], tf.float32, name="waveform")])
    def serve(self, wave):
        _, emb, _ = self.m(wave)
        return tf.reduce_mean(emb, axis=0)          # (1024,)

try:
    module = YamnetMean(yamnet)
    om, _ = tf2onnx.convert.from_function(
        module.serve,
        input_signature=[tf.TensorSpec([None], tf.float32, name="waveform")],
        opset=17)
    onnx.save(om, str(UNIVERSAL / "yamnet.onnx"))
    print("ONNX yamnet: OK (waveform → embedding 1024)")
except Exception as e:
    print(f"!! konversi yamnet.onnx gagal ({str(e)[:100]}) — API akan fallback ke runtime TF-Hub")

# Salinan SavedModel utk fallback runtime API (tanpa internet)
try:
    cached = hub.resolve(YAMNET_URL)
    dst = MODELS_DIR / "yamnet_tfhub"
    if not dst.exists():
        shutil.copytree(cached, dst)
        print("Salinan TF-Hub SavedModel:", dst)
except Exception as e:
    print(f"!! salinan tfhub gagal: {str(e)[:80]}")

# ─── Simpan encoder + metadata ───────────────────────────────────────────────
import joblib
joblib.dump(le, PREPROC / "label_encoder.pkl")

report_path = MODELS_DIR / "training_report.json"
report = json.loads(report_path.read_text(encoding="utf-8")) if report_path.exists() else {}
report["created"] = time.strftime("%Y-%m-%d %H:%M:%S")
report["classes"] = classes
report["stage2_yamnet_accuracy"] = round(float(acc), 4)
report["pipeline"] = "waveform16k → yamnet.onnx → embedding 1024 → model.onnx (head) ; gate tetap mel 27648"
report_path.write_text(json.dumps(report, indent=2), encoding="utf-8")

labels_payload = {
    "model": "engine_fault_detector_v3 (yamnet transfer learning)",
    "created": report["created"],
    "classes": classes,
    "gate": {"file": "engine_gate.onnx", "output": "gate_probabilities",
             "meaning": ["bukan_mesin", "mesin"], "threshold": 0.5,
             "input": "mel 27648 (tidak berubah)"},
    "head": {"file": "model.onnx", "input": "yamnet_embedding", "dim": 1024,
             "output": "class_probabilities"},
    "frontend": {"file": "yamnet.onnx", "input": "waveform", "sr": SR_Y,
                 "samples": LEN_Y, "output": "embedding 1024 (mean frames)"},
    "framework": "yamnet (tf-hub) → onnx (opset 17)",
}
(MODELS_DIR / "labels.json").write_text(json.dumps(labels_payload, ensure_ascii=False,
                                                   indent=2), encoding="utf-8")
print("\n🎉 YAMNET SELESAI — model.onnx (head) + yamnet.onnx + encoder + metadata")
