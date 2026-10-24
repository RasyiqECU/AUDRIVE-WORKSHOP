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


def _load_dummy():
    """Mode development tanpa model asli — return random prediction."""
    logger.warning("🟡 DUMMY mode aktif — prediksi bersifat acak. Taruh model asli lalu ubah MODEL_BACKEND.")
    return "dummy"


# ─── Main Load Function ───────────────────────────────────────────────────────

def load_all() -> None:
    """
    Dipanggil satu kali saat startup FastAPI.
    Urutan prioritas: ONNX → PyTorch → Keras → Dummy
    Override dengan env MODEL_BACKEND=onnx|pytorch|keras|dummy
    """
    _loaded["scaler"]        = _load_scaler()
    _loaded["label_encoder"] = _load_label_encoder()

    backend = config.MODEL_BACKEND.lower()

    if backend == "onnx" or (backend == "auto" and config.ONNX_FILE.exists()):
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
    """Return daftar kelas dari label encoder atau config."""
    enc = _loaded.get("label_encoder")
    if enc is not None and hasattr(enc, "classes_"):
        return list(enc.classes_)
    return config.CLASSES
