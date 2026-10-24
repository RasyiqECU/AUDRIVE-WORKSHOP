"""
AUDrive AI Service — Konfigurasi Terpusat
Semua path model dan parameter diatur di sini.
"""

import os
from pathlib import Path
from dotenv import load_dotenv

load_dotenv()

# ─── Direktori Root ────────────────────────────────────────────────────────────
BASE_DIR = Path(__file__).resolve().parent

# ─── Folder Model (DROP FILE ANDA DI SINI) ───────────────────────────────────
MODELS_DIR       = BASE_DIR / "models"
PREPROCESSING_DIR = MODELS_DIR / "preprocessing"   # .pkl  (Scaler, LabelEncoder)
CORE_DIR         = MODELS_DIR / "core"              # .pth atau .keras
UNIVERSAL_DIR    = MODELS_DIR / "universal"         # .onnx
EDGE_DIR         = MODELS_DIR / "edge"              # .tflite

# ─── Nama File Model (ubah sesuai file Anda) ─────────────────────────────────
SCALER_FILE      = PREPROCESSING_DIR / "scaler.pkl"
ENCODER_FILE     = PREPROCESSING_DIR / "label_encoder.pkl"
PYTORCH_FILE     = CORE_DIR / "model.pth"
KERAS_FILE       = CORE_DIR / "model.keras"
ONNX_FILE        = UNIVERSAL_DIR / "model.onnx"
TFLITE_FILE      = EDGE_DIR / "model.tflite"

# ─── Backend Engine: "onnx" | "pytorch" | "keras" | "dummy" ─────────────────
# "dummy" = mode placeholder tanpa model asli (untuk development)
MODEL_BACKEND = os.getenv("MODEL_BACKEND", "dummy")

# ─── Kelas Kerusakan Otomotif ─────────────────────────────────────────────────
CLASSES = [
    "normal",                  # Mesin Normal / Idle Sehat
    "timing_chain_belt",       # Kerusakan Timing Chain / Belt
    "valve_lifter",            # Kerusakan Katup / Lifter
    "rod_knock",               # Rod Knock / Bantalan Mesin
    "combustion_misfire",      # Masalah Pembakaran / Misfire
    "exhaust_leak",            # Kebocoran Knalpot
    "bearing_alternator",      # Bearing Alternator / Pulley
]

# ─── Level Urgensi per Kelas ──────────────────────────────────────────────────
# safe=aman | monitor=perlu dipantau | urgent=perlu segera | emergency=darurat
URGENCY_MAP = {
    "normal":             "safe",
    "timing_chain_belt":  "urgent",
    "valve_lifter":       "urgent",
    "rod_knock":          "emergency",
    "combustion_misfire": "urgent",
    "exhaust_leak":       "monitor",
    "bearing_alternator": "monitor",
}

# ─── Ambang Batas Confidence untuk Auto Manual Review ────────────────────────
CONFIDENCE_THRESHOLD   = 0.60   # Di bawah ini → status manual_review
QUALITY_THRESHOLD      = 0.40   # Di bawah ini → minta ulang rekaman

# ─── Parameter Ekstraksi Audio ────────────────────────────────────────────────
SAMPLE_RATE   = 22050
N_MFCC        = 40
N_MELS        = 128
HOP_LENGTH    = 512
N_FFT         = 2048
DURATION      = 5       # detik (audio dipotong/padded ke durasi ini)

# ─── API Settings ─────────────────────────────────────────────────────────────
API_HOST      = os.getenv("API_HOST", "0.0.0.0")
API_PORT      = int(os.getenv("API_PORT", "8001"))
API_SECRET    = os.getenv("API_SECRET", "")

# ─── Output ───────────────────────────────────────────────────────────────────
SPECTROGRAM_DIR = BASE_DIR / "output" / "spectrograms"

# Buat semua direktori jika belum ada
for d in [PREPROCESSING_DIR, CORE_DIR, UNIVERSAL_DIR, EDGE_DIR, SPECTROGRAM_DIR]:
    d.mkdir(parents=True, exist_ok=True)
