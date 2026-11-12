"""
app/loader.py
Dynamic Model Loader — membaca model dari folder saat startup.
Dukung: ONNX (produksi), PyTorch, Keras, dan Dummy (development).
"""

import logging
import os
from pathlib import Path
from typing import Any

import joblib
import numpy as np

import config

logger = logging.getLogger(__name__)


# ─── Singleton Container ──────────────────────────────────────────────────────
_loaded: dict[str, Any] = {
    "scaler":        None,
    "label_encoder": None,
    "model":         None,
    "gate":          None,   # model gate "mesin / bukan mesin" (opsional)
    "yamnet":        None,   # frontend embedding YAMNet: (kind, obj) — opsional
    "backend":       None,
}


def get_model_bundle() -> dict[str, Any]:
    return _loaded


# ─── Preprocessing Loaders ────────────────────────────────────────────────────

def _load_scaler() -> Any | None:
    path = config.SCALER_FILE
    if path.exists():
        scaler = joblib.load(path)
        logger.info(f"✅ Scaler loaded: {path.name}")
        return scaler
    logger.warning(f"⚠️  Scaler tidak ditemukan di {path} — lanjut tanpa scaling.")
    return None


def _load_label_encoder() -> Any | None:
    path = config.ENCODER_FILE
    if path.exists():
        enc = joblib.load(path)
        logger.info(f"✅ LabelEncoder loaded: {path.name}")
        return enc
    logger.warning(f"⚠️  LabelEncoder tidak ditemukan di {path} — pakai config.CLASSES.")
    return None


# ─── Model Loaders ───────────────────────────────────────────────────────────

def _load_onnx():
    import onnxruntime as ort
    path = config.ONNX_FILE
    if not path.exists():
        raise FileNotFoundError(f"ONNX model tidak ada: {path}")
    sess = ort.InferenceSession(str(path), providers=["CPUExecutionProvider"])
    logger.info(f"✅ ONNX model loaded: {path.name}")
    return sess


def _load_gate():
    """Gate 'mesin / bukan mesin' (opsional) — model ONNX output 2 probabilitas."""
    import onnxruntime as ort
    path = config.GATE_FILE
    if not path.exists():
        logger.warning("⚠️  Gate model tidak ditemukan — prediksi berjalan 1 tahap tanpa gate.")
        return None
    sess = ort.InferenceSession(str(path), providers=["CPUExecutionProvider"])
    logger.info(f"✅ Gate model loaded: {path.name}")
    return sess


def _load_yamnet() -> tuple[str | None, Any | None]:
    """Frontend embedding YAMNet (opsional).

    Prioritas: yamnet.onnx (ringan) → fallback runtime TF-Hub dari salinan
    lokal models/yamnet_tfhub/ (tanpa internet) → None (pipeline lama).
    """
    import onnxruntime as ort

    if config.YAMNET_FILE.exists():
        sess = ort.InferenceSession(str(config.YAMNET_FILE),
                                    providers=["CPUExecutionProvider"])
        logger.info(f"✅ YAMNet frontend loaded: {config.YAMNET_FILE.name} (ONNX)")
        return "onnx", sess

    if config.TFHUB_YAMNET_DIR.exists():
        try:
            import tensorflow as tf
            import tensorflow_hub as hub
            module = hub.load(str(config.TFHUB_YAMNET_DIR))
            logger.info("✅ YAMNet frontend loaded: TF-Hub lokal (fallback)")
            return "tfhub", module
        except Exception as e:
            logger.warning(f"⚠️  TF-Hub YAMNet gagal dimuat ({e})")

    logger.warning("⚠️  YAMNet frontend tidak ditemukan — pipeline embedding tidak aktif.")
    return None, None


def _load_pytorch():
    import torch
    path = config.PYTORCH_FILE
    if not path.exists():
        raise FileNotFoundError(f"PyTorch model tidak ada: {path}")

    # ── Sesuaikan import class model Anda di sini ─────────────────────────────
    # from your_model_module import YourModelClass
    # model = YourModelClass(...)
    # model.load_state_dict(torch.load(path, map_location="cpu"))
    # model.eval()
    # ─────────────────────────────────────────────────────────────────────────
    model = torch.load(path, map_location="cpu")   # jika save full model
    model.eval()
    logger.info(f"✅ PyTorch model loaded: {path.name}")
    return model


def _load_keras():
    import tensorflow as tf
    path = config.KERAS_FILE
    if not path.exists():
        raise FileNotFoundError(f"Keras model tidak ada: {path}")
    model = tf.keras.models.load_model(str(path))
    logger.info(f"✅ Keras model loaded: {path.name}")
    return model


def _load_xgboost():
    import xgboost  # noqa: F401 — pastikan library ter-install sebelum unpickle
    path = config.XGB_FILE
    if not path.exists():
        raise FileNotFoundError(f"XGBoost model tidak ada: {path}")
    model = joblib.load(path)
    logger.info(f"✅ XGBoost model loaded: {path.name} "
                f"({getattr(model, 'n_features_in_', '?')} fitur)")
    return model


def _load_dummy():
    """Mode development tanpa model asli — return random prediction."""
    logger.warning("🟡 DUMMY mode aktif — prediksi bersifat acak. Taruh model asli lalu ubah MODEL_BACKEND.")
    return "dummy"


# ─── Main Load Function ───────────────────────────────────────────────────────

def load_all() -> None:
    """
    Dipanggil satu kali saat startup FastAPI.
    Urutan prioritas: XGBoost → ONNX → PyTorch → Keras → Dummy
    Override dengan env MODEL_BACKEND=xgboost|onnx|pytorch|keras|dummy
    """
    _loaded["scaler"]        = _load_scaler()
    _loaded["label_encoder"] = _load_label_encoder()
    _loaded["gate"]          = _load_gate()   # opsional: gate 2 tahap
    _loaded["yamnet"]        = _load_yamnet() # opsional: frontend embedding

    backend = config.MODEL_BACKEND.lower()

    if backend == "xgboost" or (backend == "auto" and config.XGB_FILE.exists()):
        _loaded["model"]   = _load_xgboost()
        _loaded["backend"] = "xgboost"

    elif backend == "onnx" or (backend == "auto" and config.ONNX_FILE.exists()):
        _loaded["model"]   = _load_onnx()
        _loaded["backend"] = "onnx"

    elif backend == "pytorch" or (backend == "auto" and config.PYTORCH_FILE.exists()):
        _loaded["model"]   = _load_pytorch()
        _loaded["backend"] = "pytorch"

    elif backend == "keras" or (backend == "auto" and config.KERAS_FILE.exists()):
        _loaded["model"]   = _load_keras()
        _loaded["backend"] = "keras"

    else:
        _loaded["model"]   = _load_dummy()
        _loaded["backend"] = "dummy"

    logger.info(f"🚀 Model backend aktif: {_loaded['backend'].upper()}")


def get_classes() -> list[str]:
    """Return daftar kelas — urutan harus sama dengan output probabilitas model."""
    enc = _loaded.get("label_encoder")
    if enc is not None and hasattr(enc, "classes_"):
        return list(enc.classes_)
    # label_map berbentuk dict {label: index} → urutkan berdasarkan index
    if isinstance(enc, dict):
        return [k for k, _ in sorted(enc.items(), key=lambda kv: kv[1])]
    return config.CLASSES
