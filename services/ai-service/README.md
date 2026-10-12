# AUDrive AI Sound Scanning Service


FastAPI microservice untuk analisis suara kendaraan menggunakan deep learning.


## Fitur


- Analisis audio kendaraan (WAV, MP3, M4A)
- Deteksi kerusakan mesin berdasarkan suara
- Confidence score untuk setiap prediksi
- Rekomendasi perbaikan otomatis


## Kategori Kerusakan


- Engine knocking
- Bearing abnormal
- Belt/roller worn
- Exhaust leak
- Idle unstable
- Transmission noise


## Instalasi


### Prerequisites


- Python 3.9+
- pip


### Setup


1. Buat virtual environment:
```bash
python -m venv venv
source venv/bin/activate  # Linux/Mac
# atau
venv\Scripts\activate  # Windows
```


2. Install dependencies:
```bash
pip install -r requirements.txt
```


3. Jalankan service:
```bash
python main.py
```


Service akan berjalan di `http://localhost:8001`


## API Endpoints


### Health Check
```
GET /
GET /health
```


### Analyze Audio
```
POST /api/v1/analyze
Content-Type: multipart/form-data


Parameters:
- audio: File audio (WAV, MP3, M4A, max 20MB)


Response:
{
  "predicted_class": "engine_knocking",
  "confidence": 0.87,
  "recommendations": [
    "Periksa busi dan ganti jika diperlukan",
    "Cek tekanan kompresi mesin",
    ...
  ],
  "spectrogram_url": null
}
```


## Konfigurasi Laravel


Update `.env` Laravel:
```env
AI_DRIVER=fastapi
AI_SERVICE_BASE_URL=http://localhost:8001
AI_SERVICE_TIMEOUT=3
```


Untuk development tanpa AI service:
```env
AI_DRIVER=fake
```


## Development


### Menambah Model ML


Untuk mengimplementasikan model deep learning yang sebenarnya:


1. Latih model menggunakan dataset audio kendaraan
2. Export model ke format yang kompatibel (PyTorch/TensorFlow)
3. Implementasikan inference di `main.py` pada bagian TODO
4. Generate spectrogram dan simpan ke storage


### Testing


```bash
# Test endpoint
curl -X POST "http://localhost:8001/api/v1/analyze" \
  -H "Content-Type: multipart/form-data" \
  -F "audio=@test_audio.wav"
```


## Troubleshooting


### Service tidak bisa diakses dari Laravel
- Pastikan service berjalan di port 8001
- Cek firewall dan network settings
- Update `AI_SERVICE_BASE_URL` di `.env` Laravel


### File audio terlalu besar
- Maksimal ukuran file: 20MB
- Kompres audio jika diperlukan
- Gunakan format yang efisien (MP3 > WAV)


### Model tidak akurat
- Pastikan audio berkualitas baik
- Hindari noise background berlebih
- Gunakan rekaman minimal 5 detik


## License


Proprietary - AUDrive © 2026



