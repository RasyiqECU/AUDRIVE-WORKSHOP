"""
app/schemas.py
Pydantic schemas — sesuai workflow 4 langkah diagnostik suara mesin.
"""

from typing import Optional
from pydantic import BaseModel, Field


# ─── Langkah 1: Validasi Kualitas Audio ──────────────────────────────────────

class AudioQuality(BaseModel):
    is_valid: bool
    quality_score: float = Field(..., ge=0.0, le=1.0, description="Skor kualitas 0-1")
    duration_sec: float
    sample_rate: int
    warning: Optional[str] = None       # pesan jika kualitas buruk


# ─── Langkah 2 & 3: Hasil Diagnostik AI ──────────────────────────────────────

class WaveAnalysis(BaseModel):
    """Karakteristik gelombang suara yang terdeteksi."""
    dominant_frequency_hz: Optional[float] = None
    pattern_description: str       # deskripsi pola suara untuk customer
    anomaly_detected: bool
    anomaly_details: Optional[str] = None


class UrgencyDecision(BaseModel):
    """Keputusan urgensi kunjungan ke bengkel."""
    level: str                     # safe | monitor | urgent | emergency
    label: str                     # label ramah bahasa Indonesia
    action: str                    # tindakan yang disarankan
    icon: str                      # emoji untuk UI


class DiagnosticResult(BaseModel):
    """Hasil diagnostik lengkap dari AI (Langkah 3)."""
    # Identifikasi kerusakan
    predicted_class: str
    class_label: str               # label bahasa Indonesia
    confidence: float = Field(..., ge=0.0, le=1.0)
    top3: list[dict]

    # Analisis gelombang (untuk customer & mekanik)
    wave_analysis: WaveAnalysis
    urgency: UrgencyDecision

    # Rekomendasi
    recommendations: list[str]     # untuk customer (bahasa friendly)
    mechanic_notes: str            # untuk dashboard mekanik (teknis)

    # Metadata
    spectrogram_path: Optional[str] = None
    audio_quality: AudioQuality
    inference_ms: float
    backend: str
    is_dummy: bool
    needs_manual_review: bool      # True jika confidence < threshold atau quality rendah


# ─── Response API ─────────────────────────────────────────────────────────────

class PredictResponse(BaseModel):
    success: bool
    scan_id: Optional[int] = None
    result: Optional[DiagnosticResult] = None
    error: Optional[str] = None
    quality_error: Optional[str] = None   # jika audio tidak valid → minta ulang


class HealthResponse(BaseModel):
    status: str
    backend: str
    scaler_loaded: bool
    encoder_loaded: bool
    model_loaded: bool
    classes: list[str]
    urgency_map: dict
