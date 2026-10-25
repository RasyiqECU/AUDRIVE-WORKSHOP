# AUDrive

Aplikasi manajemen dealership & workshop otomotif berbasis **Laravel 13**, dilengkapi **AI Diagnostik Suara Mesin** sebagai microservice Python/FastAPI di folder `ai-service/`.

## Struktur Project

Root aplikasi adalah folder `audrive/` — bukan `audrive/laravel/`. Tidak ada lagi salinan aplikasi kedua.

```text
audrive/
├── ai-service/          # Python/FastAPI — AI Diagnostik Suara Mesin (port 8001)
├── app/                 # Kode aplikasi Laravel (Controllers, Models, Enums, Services, ...)
├── bootstrap/           # Bootstrap Laravel (app.php + cache/)
├── config/              # Konfigurasi Laravel (ai_service.php, database.php, ...)
├── database/            # Migrations, factories, seeders
├── docker/              # Placeholder konfigurasi Docker (saat ini kosong)
├── public/              # Entrypoint web + hasil build Vite (public/build)
├── resources/           # Blade views, CSS, JS
├── routes/              # Definisi route (web, api, admin, customer, marketplace, ...)
├── storage/             # Log, cache, upload user (audio/mobil/spectrogram)
├── tests/               # PHPUnit tests
├── vendor/              # Dependensi Composer (di-install, jangan diedit)
├── node_modules/        # Dependensi npm (di-install, jangan diedit)
├── artisan              # CLI Laravel
├── composer.json        # Manifest dependensi PHP
├── package.json         # Manifest dependensi JS
├── .env                 # Kredensial aktif (jangan di-commit)
├── .env.example         # Template environment
└── STRUCTURE.md         # Pendokumentasian struktur folder lengkap
```

## Prasyarat

- PHP 8.3+
- Composer 2.x
- Node.js (npm) 18+
- Python 3.12+ (untuk ai-service)
- MySQL (database Laravel aktif)

## Menjalankan Laravel (dari root `audrive/`)

```bash
composer install
cp .env.example .env       # jika .env belum ada
php artisan key:generate
npm install
npm run build              # build asset produksi
php artisan serve          # web di http://localhost:8000
```

Development mode (asset live-reload):

```bash
npm run dev                # Vite dev server
php artisan serve          # terminal terpisah
```

## Menjalankan AI Service (Python)

```bash
cd ai-service

# pertama kali: buat venv lokal & install dependensi
python -m venv .venv
.venv\Scripts\activate     # Windows  |  source .venv/bin/activate (Linux/macOS)
pip install -r requirements.txt

# jalankan service (port 8001)
uvicorn main:app --host 0.0.0.0 --port 8001 --reload
```

- Model ML (.pkl/.pth/.keras/.onnx/.tflite) ditaruh manual di `ai-service/models/…` — lihat `ai-service/models/README.txt`.
- Tanpa model, service berjalan dengan `MODEL_BACKEND=dummy`.
- Konfigurasi Python: `ai-service/.env` (copy dari `ai-service/.env.example`).

## Integrasi Laravel ↔ Python

- Komunikasi **via HTTP** (bukan path file): Laravel `App\Services\AiScanService` mengirim audio ke `POST /predict`, mengunduh spectrogram dari `GET /spectrogram/{filename}`, dan health check `GET /health`.
- Konfigurasi endpoint ada di `config/ai_service.php`, diisi dari `.env` Laravel:
  `AI_SERVICE_URL`, `AI_SERVICE_SECRET`, `AI_SERVICE_TIMEOUT`, `AI_SERVICE_RETRY`.
- Python memvalidasi secret lewat header `X-Api-Secret` (`API_SECRET` di `ai-service/.env`).

## Menjalankan Test

```bash
# Laravel
php artisan test

# Python (dari ai-service/)
python -m compileall -q .      # cek sintaks
.venv\Scripts\python -m pytest # atau test suite lain jika ditambahkan
```

## Catatan Penting

- `.env` dan `database/database.sqlite` **tidak** dilacak Git.
- `node_modules/`, `vendor/`, `.venv/`, hasil build (`public/build`), cache, dan log diabaikan Git.
- Database aktif adalah **MySQL** (lihat `.env`), migration tidak dijalankan otomatis oleh tooling ini.
