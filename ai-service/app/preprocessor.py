"""
app/preprocessor.py
Langkah 1 & 2 — Validasi audio + ekstraksi fitur MFCC/Mel-Spectrogram.
"""

import io
import logging
import uuid
from pathlib import Path

import librosa
import numpy as np

import config

logger = logging.getLogger(__name__)


# ─── Langkah 1: Validasi & Load Audio ────────────────────────────────────────

def validate_audio(audio_bytes: bytes) -> dict:
    """
    Validasi kualitas audio sebelum diproses.
    Return dict dengan is_valid, quality_score, warning, dll.
    """
    try:
        y, sr = _load_raw(audio_bytes)
    except Exception as e:
        return {
            "is_valid": False,
            "quality_score": 0.0,
            "duration_sec": 0.0,
            "sample_rate": 0,
            "warning": f"File audio tidak dapat dibaca. Pastikan format WAV/MP3/M4A dan file tidak rusak. Detail: {e}",
        }

    duration = len(y) / sr
    issues = []

    # 1. Cek durasi minimal
    if duration < 3.0:
        issues.append("Durasi rekaman terlalu pendek (minimal 3 detik).")

    # 2. Cek silence / volume terlalu rendah
    rms = float(np.sqrt(np.mean(y ** 2)))
    if rms < 0.005:
        issues.append("Volume suara terlalu rendah atau hampir hening — dekatkan mikrofon ke sumber suara mesin.")

    # 3. Cek clipping (terlalu keras / terdistorsi)
    clip_ratio = float(np.mean(np.abs(y) > 0.98))
    if clip_ratio > 0.05:
        issues.append("Rekaman mengalami distorsi/clipping — jauhkan sedikit mikrofon dari sumber suara.")

    # 4. Hitung SNR proxy (signal vs noise floor)
    signal_power = float(np.percentile(np.abs(y), 90))
    noise_floor  = float(np.percentile(np.abs(y), 10))
    snr_proxy    = signal_power / (noise_floor + 1e-9)
    if snr_proxy < 5:
        issues.append("Terlalu banyak noise background — rekam di tempat lebih tenang jika memungkinkan.")

    # Hitung quality score 0–1
    quality_score = _compute_quality_score(duration, rms, clip_ratio, snr_proxy)
    is_valid      = quality_score >= config.QUALITY_THRESHOLD and not any(
        "tidak dapat dibaca" in i or "terlalu pendek" in i for i in issues
    )

    warning = None
    if issues:
        warning = " | ".join(issues)
        if not is_valid:
            warning += " Mohon unggah ulang rekaman dengan kualitas lebih baik."

    return {
        "is_valid":      is_valid,
        "quality_score": round(quality_score, 3),
        "duration_sec":  round(duration, 2),
        "sample_rate":   sr,
        "warning":       warning,
    }


def _compute_quality_score(duration: float, rms: float, clip_ratio: float, snr: float) -> float:
    """Skor kualitas sederhana 0-1."""
    score = 1.0
    if duration < 3:    score -= 0.4
    elif duration < 5:  score -= 0.1
    if rms < 0.005:     score -= 0.3
    if clip_ratio > 0.05: score -= 0.2
    if snr < 5:         score -= 0.2
    return max(0.0, min(1.0, score))


# ─── Load Audio ───────────────────────────────────────────────────────────────

def _load_raw(audio_bytes: bytes) -> tuple[np.ndarray, int]:
    audio_io = io.BytesIO(audio_bytes)
    y, sr = librosa.load(audio_io, sr=config.SAMPLE_RATE, mono=True)
    return y, sr


def load_audio(audio_bytes: bytes) -> tuple[np.ndarray, int]:
    try:
        return _load_raw(audio_bytes)
    except Exception as e:
        logger.error(f"Gagal load audio: {e}")
        raise ValueError(f"Format audio tidak valid: {e}")


def normalize_duration(y: np.ndarray, sr: int) -> np.ndarray:
    target_len = sr * config.DURATION
    if len(y) > target_len:
        return y[:target_len]
    return np.pad(y, (0, target_len - len(y)), mode="constant")


# ─── Langkah 2: Ekstraksi Fitur ──────────────────────────────────────────────

def extract_mfcc(y: np.ndarray, sr: int) -> np.ndarray:
    """
    MFCC mean + std → feature vector (N_MFCC * 2,)
    Dipakai untuk MLP / klasifikasi tabular.
    """
    mfcc = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=config.N_MFCC,
                                 n_fft=config.N_FFT, hop_length=config.HOP_LENGTH)
    return np.concatenate([np.mean(mfcc, axis=1), np.std(mfcc, axis=1)])


def extract_mel_spectrogram(y: np.ndarray, sr: int) -> np.ndarray:
    """Mel-Spectrogram 2D (N_MELS, time) — untuk CNN."""
    mel = librosa.feature.melspectrogram(y=y, sr=sr, n_mels=config.N_MELS,
                                         n_fft=config.N_FFT, hop_length=config.HOP_LENGTH)
    return librosa.power_to_db(mel, ref=np.max)


def extract_spectral_features(y: np.ndarray, sr: int) -> dict:
    """
    Fitur spektral tambahan untuk analisis deskriptif gelombang.
    Dipakai untuk mengisi WaveAnalysis pada hasil diagnostik.
    """
    # Dominant frequency via spectral centroid
    centroid  = librosa.feature.spectral_centroid(y=y, sr=sr)[0]
    dom_freq  = float(np.mean(centroid))

    # Zero crossing rate → proxy untuk suara berisik/tajam
    zcr = float(np.mean(librosa.feature.zero_crossing_rate(y)))

    # RMS energy
    rms = float(np.sqrt(np.mean(y ** 2)))

    # Spectral rolloff (frekuensi batas 85% energi)
    rolloff = float(np.mean(librosa.feature.spectral_rolloff(y=y, sr=sr, roll_percent=0.85)))

    # Onset strength (kuat/lemahnya ketukan)
    onset_env = librosa.onset.onset_strength(y=y, sr=sr)
    onset_mean = float(np.mean(onset_env))

    return {
        "dominant_frequency_hz": round(dom_freq, 1),
        "zero_crossing_rate":   round(zcr, 4),
        "rms_energy":           round(rms, 4),
        "spectral_rolloff_hz":  round(rolloff, 1),
        "onset_strength":       round(onset_mean, 4),
    }


def save_spectrogram_image(y: np.ndarray, sr: int) -> str:
    """Simpan Mel-Spectrogram sebagai PNG."""
    import matplotlib
    matplotlib.use("Agg")
    import matplotlib.pyplot as plt

    mel_db   = extract_mel_spectrogram(y, sr)
    filename = f"{uuid.uuid4().hex}.png"
    out_path = config.SPECTROGRAM_DIR / filename

    fig, ax = plt.subplots(figsize=(10, 4), facecolor="#0d0f14")
    ax.set_facecolor("#0d0f14")
    librosa.display.specshow(mel_db, sr=sr, hop_length=config.HOP_LENGTH,
                             x_axis="time", y_axis="mel", ax=ax, cmap="magma")
    ax.set_title("Mel-Spectrogram — AUDrive AI Scan", color="#c8cacc", fontsize=10)
    ax.tick_params(colors="#8a8d94")
    for spine in ax.spines.values():
        spine.set_edgecolor("#2a2d35")
    plt.tight_layout()
    fig.savefig(out_path, dpi=100, bbox_inches="tight", facecolor="#0d0f14")
    plt.close(fig)

    return str(out_path)


# ─── Pipeline Lengkap ─────────────────────────────────────────────────────────

def preprocess_audio(audio_bytes: bytes) -> dict:
    """
    Pipeline preprocessing lengkap.
    Asumsi: validate_audio() sudah dipanggil sebelumnya dan hasilnya is_valid=True.
    """
    y, sr = load_audio(audio_bytes)
    y     = normalize_duration(y, sr)

    spectral = extract_spectral_features(y, sr)

    return {
        "mfcc":             extract_mfcc(y, sr),
        "mel_spectrogram":  extract_mel_spectrogram(y, sr),
        "mel_3d":           extract_mel_spectrogram(y, sr)[np.newaxis, ...],
        "spectral":         spectral,
        "spectrogram_path": save_spectrogram_image(y, sr),
        "duration_sec":     len(y) / sr,
        "sample_rate":      sr,
    }
