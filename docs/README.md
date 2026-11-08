# AUDrive

Aplikasi manajemen dealership & workshop otomotif berbasis **Laravel 13**,
dilengkapi **AI Diagnostik Suara Mesin** sebagai microservice Python/FastAPI
di folder `ai-service/` (backend model: XGBoost).

Root aplikasi = root repository ini (tidak ada subfolder aplikasi lagi).

## Folder Dokumentasi

Semua dokumentasi project terkumpul di [`docs/`](.):

| File | Isi |
|---|---|
| `docs/README.md` | (file ini) Panduan menjalankan & prasyarat |
| `docs/STRUCTURE.md` | Peta struktur folder & file konfigurasi |
| `docs/AUDrive_Context.md` | Konteks project, arsitektur, workflow AI Scan |
| `docs/QA_CHECKLIST.md` | Status QA, hasil test, roadmap hardening |

> `AGENTS.md` dan `CLAUDE.md` di root adalah **konfigurasi tooling agent**
> (harus di root agar ter-detect otomatis), bukan dokumentasi project.

## Prasyarat

- **PHP ≥ 8.4.1** (Symfony 8.1 dependensi Laravel 13 menuntut 8.4.1, bukan 8.3)
- Composer 2.x
- Node.js (npm) 18+
- Python 3.12+ (untuk ai-service)
- MySQL (database aplikasi; test memakai SQLite in-memory)

## Menjalankan Laravel

```bash
composer install
cp .env.example .env       # jika .env belum ada; set DB_CONNECTION=mysql
php artisan key:generate
npm install
npm run build              # build asset produksi
php artisan storage:link   # wajib — agar spectrogram & audio tampil di web
php artisan migrate --seed # buat semua tabel + akun demo
php artisan serve          # web di http://localhost:8000
```

Development mode (asset live-reload):

```bash
npm run dev                # Vite dev server  (terminal 1)
php artisan serve          # (terminal 2)
php artisan queue:work     # (terminal 3) wajib untuk AI scan & job lain
```

Akun demo (setelah `db:seed`, password: `password`):
`admin@audrive.local`, `manager@audrive.local`, `sales@audrive.local`,
`mechanic@audrive.local`, `cashier@audrive.local`, `inventory@audrive.local`,
`customer@audrive.local`.

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

- Model ML ditaruh manual di `ai-service/models/…` — lihat `ai-service/models/README.txt`.
- Backend dipilih lewat `MODEL_BACKEND` di `ai-service/.env`
  (`xgboost` | `onnx` | `pytorch` | `keras` | `dummy`).
- Secret FE/BE: `AI_SERVICE_SECRET` (Laravel .env) harus sama dengan
  `API_SECRET` (`ai-service/.env`).
- Detail integrasi = [`docs/AUDrive_Context.md`](AUDrive_Context.md).

## Menjalankan Test

```bash
# Laravel (Pest — bukan PHPUnit, walau runner di bawahnya tetap phpunit.xml)
composer test

# Python (dari ai-service/)
python -m compileall -q .      # cek sintaks
.venv\Scripts\python -m pytest # jika test suite Python ditambahkan
```

> Test memakai **database MySQL khusus** `audrive_test` (TERPISAH dari database
> aplikasi `audrive`) — dibuat saat setup lewat:
> `CREATE DATABASE audrive_test;` — refresh per-run ditangani `RefreshDatabase`.
> Pastikan MySQL (XAMPP) berjalan saat menjalankan test.

## Docker

Folder `docker/` **belum diimplementasikan** — `docker-compose.yml` masih
placeholder kosong. Hanya basisnya untuk `docker compose up` di masa depan.

## Catatan Penting

- `.env`, `vendor/`, `node_modules/`, `.venv/`, `public/build`, `public/storage`
  (symlink), cache, log, dan `storage/app/public` tidak dilacak Git.
- Database aktif aplikasi = **MySQL** (lihat `.env`); migration tidak dijalankan otomatis.
- Job processing memakai queue driver `database` — jalankan `php artisan queue:work`.
