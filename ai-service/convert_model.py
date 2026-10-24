"""
convert_model.py
Script konversi model satu-klik:
    .pth (PyTorch) → .onnx → .tflite
    .keras         → .onnx → .tflite

Cara pakai:
    python convert_model.py --source pytorch   # convert .pth → .onnx → .tflite
    python convert_model.py --source keras     # convert .keras → .onnx → .tflite
    python convert_model.py --source pytorch --skip-tflite   # hanya sampai .onnx
"""

import argparse
import logging
import sys
from pathlib import Path

import numpy as np

# Tambahkan root ke sys.path
sys.path.insert(0, str(Path(__file__).parent))
import config

logging.basicConfig(level=logging.INFO, format="%(asctime)s [%(levelname)s] %(message)s")
logger = logging.getLogger("convert_model")


# ─── Step 1: PyTorch → ONNX ──────────────────────────────────────────────────

def pytorch_to_onnx(input_size: int = config.N_MFCC * 2) -> Path:
    """
    Konversi .pth → .onnx
    Sesuaikan `input_size` dengan ukuran feature vector model Anda.
    """
    try:
        import torch
    except ImportError:
        logger.error("Install PyTorch: pip install torch")
        raise

    pth_path  = config.PYTORCH_FILE
    onnx_path = config.ONNX_FILE

    if not pth_path.exists():
        raise FileNotFoundError(f"File .pth tidak ditemukan: {pth_path}")

    logger.info(f"📦 Loading PyTorch model dari {pth_path} ...")

    # ── Sesuaikan dengan class model Anda ─────────────────────────────────────
    # from your_module import YourModelClass
    # model = YourModelClass(input_size=input_size, num_classes=len(config.CLASSES))
    # model.load_state_dict(torch.load(pth_path, map_location="cpu"))
    # ─────────────────────────────────────────────────────────────────────────
    model = torch.load(pth_path, map_location="cpu")
    model.eval()

    dummy_input = torch.randn(1, input_size, dtype=torch.float32)

    logger.info(f"🔄 Mengekspor ke ONNX: {onnx_path} ...")
    torch.onnx.export(
        model,
        dummy_input,
        str(onnx_path),
        export_params=True,
        opset_version=17,
        input_names=["mfcc_features"],
        output_names=["class_probabilities"],
        dynamic_axes={
            "mfcc_features":       {0: "batch_size"},
            "class_probabilities": {0: "batch_size"},
        },
    )
    logger.info(f"✅ ONNX tersimpan: {onnx_path}")
    return onnx_path


# ─── Step 1b: Keras → ONNX ───────────────────────────────────────────────────

def keras_to_onnx() -> Path:
    """Konversi .keras → .onnx via tf2onnx."""
    try:
        import tensorflow as tf
        import tf2onnx
    except ImportError:
        logger.error("Install: pip install tensorflow tf2onnx")
        raise

    keras_path = config.KERAS_FILE
    onnx_path  = config.ONNX_FILE

    if not keras_path.exists():
        raise FileNotFoundError(f"File .keras tidak ditemukan: {keras_path}")

    model = tf.keras.models.load_model(str(keras_path))
    logger.info(f"🔄 Konversi Keras → ONNX: {onnx_path} ...")

    input_signature = [tf.TensorSpec(model.inputs[0].shape, tf.float32, name="mfcc_features")]
    onnx_model, _ = tf2onnx.convert.from_keras(model, input_signature=input_signature, opset=17)

    import onnx
    onnx.save(onnx_model, str(onnx_path))
    logger.info(f"✅ ONNX tersimpan: {onnx_path}")
    return onnx_path


# ─── Step 2: ONNX → TFLite ───────────────────────────────────────────────────

def onnx_to_tflite(onnx_path: Path) -> Path:
    """Konversi .onnx → .tflite via onnx-tf + TFLite Converter."""
    try:
        import onnx
        import onnx_tf
        import tensorflow as tf
    except ImportError:
        logger.error("Install: pip install onnx onnx-tf tensorflow")
        raise

    tflite_path = config.TFLITE_FILE
    tmp_saved_model = config.EDGE_DIR / "_tmp_savedmodel"

    logger.info("🔄 ONNX → TensorFlow SavedModel ...")
    onnx_model = onnx.load(str(onnx_path))
    tf_rep = onnx_tf.backend.prepare(onnx_model)
    tf_rep.export_graph(str(tmp_saved_model))

    logger.info("🔄 SavedModel → TFLite ...")
    converter = tf.lite.TFLiteConverter.from_saved_model(str(tmp_saved_model))
    converter.optimizations = [tf.lite.Optimize.DEFAULT]   # quantization ringan
    tflite_model = converter.convert()

    tflite_path.write_bytes(tflite_model)
    logger.info(f"✅ TFLite tersimpan: {tflite_path} ({len(tflite_model)/1024:.1f} KB)")

    # Bersihkan tmp
    import shutil
    shutil.rmtree(tmp_saved_model, ignore_errors=True)

    return tflite_path


# ─── Validation ──────────────────────────────────────────────────────────────

def validate_onnx(onnx_path: Path, input_size: int = config.N_MFCC * 2):
    """Verifikasi ONNX model bisa inference."""
    import onnxruntime as ort
    sess  = ort.InferenceSession(str(onnx_path), providers=["CPUExecutionProvider"])
    dummy = np.random.randn(1, input_size).astype(np.float32)
    out   = sess.run(None, {sess.get_inputs()[0].name: dummy})
    logger.info(f"✅ ONNX validation OK — output shape: {out[0].shape}")


def validate_tflite(tflite_path: Path, input_size: int = config.N_MFCC * 2):
    """Verifikasi TFLite model bisa inference."""
    import tensorflow as tf
    interp = tf.lite.Interpreter(model_path=str(tflite_path))
    interp.allocate_tensors()
    in_det = interp.get_input_details()[0]
    interp.set_tensor(in_det["index"], np.random.randn(1, input_size).astype(np.float32))
    interp.invoke()
    out = interp.get_tensor(interp.get_output_details()[0]["index"])
    logger.info(f"✅ TFLite validation OK — output shape: {out.shape}")


# ─── CLI ─────────────────────────────────────────────────────────────────────

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="AUDrive Model Converter")
    parser.add_argument("--source",       choices=["pytorch", "keras"], default="pytorch")
    parser.add_argument("--skip-tflite",  action="store_true", help="Hentikan di .onnx")
    parser.add_argument("--input-size",   type=int, default=config.N_MFCC * 2,
                        help="Ukuran input vector (default: N_MFCC*2)")
    args = parser.parse_args()

    logger.info(f"🚀 Memulai konversi dari source: {args.source.upper()}")
    logger.info(f"   Input size : {args.input_size}")
    logger.info(f"   ONNX target: {config.ONNX_FILE}")
    logger.info(f"   TFLite target: {config.TFLITE_FILE}")

    # Step 1: Core model → ONNX
    if args.source == "pytorch":
        onnx_path = pytorch_to_onnx(input_size=args.input_size)
    else:
        onnx_path = keras_to_onnx()

    validate_onnx(onnx_path, input_size=args.input_size)

    # Step 2: ONNX → TFLite
    if not args.skip_tflite:
        tflite_path = onnx_to_tflite(onnx_path)
        validate_tflite(tflite_path, input_size=args.input_size)

    logger.info("")
    logger.info("=" * 50)
    logger.info("🎉 Konversi selesai!")
    logger.info(f"   ONNX   → {config.ONNX_FILE}")
    if not args.skip_tflite:
        logger.info(f"   TFLite → {config.TFLITE_FILE}")
    logger.info("Sekarang set MODEL_BACKEND=onnx di .env lalu restart API.")
    logger.info("=" * 50)
