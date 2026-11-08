# AUDrive

Aplikasi manajemen dealership & workshop otomotif — **Laravel 13** + microservice
**Python/FastAPI** untuk AI Diagnostik Suara Mesin (`ai-service/`).

## 📚 Dokumentasi

Seluruh dokumentasi project ada di folder [`docs/`](docs/):

| Dokumen | Isi |
|---|---|
| [`docs/README.md`](docs/README.md) | Cara menjalankan (Laravel, AI service, test, akun demo) |
| [`docs/AUDrive_Context.md`](docs/AUDrive_Context.md) | Konteks project, arsitektur, workflow AI Scan |
| [`docs/STRUCTURE.md`](docs/STRUCTURE.md) | Peta struktur folder & file konfigurasi |
| [`docs/QA_CHECKLIST.md`](docs/QA_CHECKLIST.md) | Status QA, hasil test suite, roadmap |

## ⚡ Mulai cepat

```bash
composer install
cp .env.example .env          # set DB_CONNECTION=mysql
php artisan key:generate
npm install && npm run build
php artisan storage:link      # penting: agar spectrogram & audio tampil
php artisan migrate --seed
php artisan serve             # http://localhost:8000
```

Butuh PHP ≥ **8.4.1**. Panduan lengkap (termasuk AI service & queue worker):
[`docs/README.md`](docs/README.md).
