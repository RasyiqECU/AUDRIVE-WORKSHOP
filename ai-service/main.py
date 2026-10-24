"""
main.py — AUDrive AI Service Entry Point (Workflow 4 Langkah)

Langkah 1: Terima & validasi kualitas audio
Langkah 2: Preprocessing + feature extraction
Langkah 3: Inference → laporan diagnostik
Langkah 4: (Laravel) Dual-diagnosis display + manual review mekanik

Cara menjalankan:
    uvicorn main:app --host 0.0.0.0 --port 8001 --reload
"""

import logging
from contextlib import asynccontextmanager

from fastapi import FastAPI, File, Form, HTTPException, Header, UploadFile
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import FileResponse

import config
from app.loader import load_all, get_model_bundle, get_classes
from app.preprocessor import validate_audio, preprocess_audio
from app.predictor import predict
from app.schemas import PredictResponse, HealthResponse

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(name)s — %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S",
)
logger = logging.getLogger(__name__)


# ─── Lifespan ────────────────────────────────────────────────────────────────
@asynccontextmanager
async def lifespan(app: FastAPI):
    logger.info("=" * 60)
    logger.info("  AUDrive AI Diagnostik Suara Mesin — Starting Up")
    logger.info(f"  Backend: {config.MODEL_BACKEND.upper()}")
    logger.info(f"  Kelas  : {', '.join(config.CLASSES)}")
    logger.info("=" * 60)
    load_all()
    yield
    logger.info("AUDrive AI Service — Shutting down.")


app = FastAPI(
    title="AUDrive AI Diagnostik Suara Mesin",
    version="2.0.0",
    description=(
        "Service deteksi kerusakan mesin berbasis analisis suara. "
        "Mengimplementasikan workflow 4 langkah: "
        "validasi audio → pemindaian AI → laporan diagnostik → review manual."
    ),
    lifespan=lifespan,
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["POST", "GET"],
    allow_headers=["*"],
)


def _check_secret(secret: str | None):
    if config.API_SECRET and secret != config.API_SECRET:
        raise HTTPException(status_code=401, detail="Invalid API secret")


# ─── Endpoints ───────────────────────────────────────────────────────────────

@app.get("/", include_in_schema=False)
async def root():
    return {"service": "AUDrive AI Diagnostik Suara Mesin", "version": "2.0.0", "status": "running"}


@app.get("/health", response_model=HealthResponse)
async def health():
    """Health check — Laravel polling endpoint."""
    bundle = get_model_bundle()
    return {
        "status":         "ok",
        "backend":        bundle.get("backend", "unknown"),
        "scaler_loaded":  bundle.get("scaler") is not None,
        "encoder_loaded": bundle.get("label_encoder") is not None,
        "model_loaded":   bundle.get("model") is not None,
        "classes":        get_classes(),
        "urgency_map":    config.URGENCY_MAP,
    }


@app.post("/predict", response_model=PredictResponse)
async def predict_endpoint(
    audio:         UploadFile   = File(..., description="File audio mesin .wav/.mp3/.m4a"),
    scan_id:       int | None   = Form(None),
    x_api_secret:  str | None   = Header(None),
):
    """
    Endpoint utama diagnostik (Langkah 1–3).

    **Langkah 1** — Validasi kualitas audio.
    Jika kualitas buruk, kembalikan quality_error agar customer mengunggah ulang.

    **Langkah 2** — Preprocessing: MFCC + Mel-Spectrogram + spectral features.

    **Langkah 3** — Inferensi model → laporan: kelas kerusakan, analisis gelombang,
    urgensi, rekomendasi customer, catatan mekanik.
    """
    _check_secret(x_api_secret)

    # Baca file
    audio_bytes = await audio.read()
    if not audio_bytes:
        raise HTTPException(400, detail="File audio kosong.")

    filename = audio.filename or "audio"
    logger.info(f"📥 Terima: {filename} ({len(audio_bytes)/1024:.1f} KB) — scan_id={scan_id}")

    # ── Langkah 1: Validasi Kualitas ─────────────────────────────────────────
    quality = validate_audio(audio_bytes)
    logger.info(f"🔍 Kualitas audio: {quality['quality_score']:.2f} | valid={quality['is_valid']}")

    if not quality["is_valid"]:
        # Kembalikan quality_error agar Laravel tampilkan pesan ke customer
        return PredictResponse(
            success=False,
            scan_id=scan_id,
            quality_error=(
                quality.get("warning") or
                "Kualitas audio tidak memadai untuk dianalisis. "
                "Mohon unggah ulang rekaman dengan posisi mikrofon "
                "lebih dekat ke sumber suara mesin (30–50 cm)."
            ),
        )

    try:
        # ── Langkah 2: Preprocessing ─────────────────────────────────────────
        features = preprocess_audio(audio_bytes)

        # ── Langkah 3: Inferensi + Susun Laporan ─────────────────────────────
        result = predict(features, quality)

        logger.info(
            f"✅ Hasil: [{result['urgency']['icon']}] {result['predicted_class']} "
            f"({result['confidence']:.1%}) | urgensi={result['urgency']['level']} "
            f"| backend={result['backend']} | {result['inference_ms']}ms"
        )

        return PredictResponse(success=True, scan_id=scan_id, result=result)

    except ValueError as e:
        logger.warning(f"⚠️  Preprocessing error: {e}")
        raise HTTPException(422, detail=str(e))
    except Exception as e:
        logger.exception(f"❌ Prediction error: {e}")
        raise HTTPException(500, detail="Terjadi kesalahan internal saat memproses audio.")


@app.get("/spectrogram/{filename}")
async def get_spectrogram(filename: str, x_api_secret: str | None = Header(None)):
    """Serve spectrogram image ke Laravel."""
    _check_secret(x_api_secret)
    path = config.SPECTROGRAM_DIR / filename
    if not path.exists():
        raise HTTPException(404, detail="Spectrogram tidak ditemukan.")
    return FileResponse(str(path), media_type="image/png")


@app.get("/classes")
async def list_classes():
    """Daftar kelas kerusakan beserta level urgensi."""
    classes = get_classes()
    return {
        "classes": [
            {
                "key":     cls,
                "urgency": config.URGENCY_MAP.get(cls, "monitor"),
            }
            for cls in classes
        ]
    }
