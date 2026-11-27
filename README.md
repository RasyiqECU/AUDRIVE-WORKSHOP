# AUDrive

Aplikasi manajemen dealership & workshop otomotif: **Laravel 13** + microservice
**Python/FastAPI** untuk diagnostik suara mesin (`ai-service/`).

## 📚 Dokumentasi

| Dokumen | Isi |
|---|---|
| [`docs/README.md`](docs/README.md) | Cara menjalankan (Laravel, scheduler, queue, AI service, test, akun demo) |
| [`docs/ROLES_MODUL_FITUR.md`](docs/ROLES_MODUL_FITUR.md) | Role, hak akses, modul, alur bisnis, aturan omzet |
| [`docs/AUDrive_Context.md`](docs/AUDrive_Context.md) | Konteks project, arsitektur, AI Scan |
| [`docs/STRUCTURE.md`](docs/STRUCTURE.md) | Peta struktur folder |
| [`docs/QA_CHECKLIST.md`](docs/QA_CHECKLIST.md) | Hasil test, masalah yang diketahui, daftar sebelum deploy |

## ⚡ Mulai cepat

Butuh PHP ≥ 8.4.1, Composer, Node.js, dan MySQL.

```bash
composer install
cp .env.example .env          # ubah DB_CONNECTION=mysql dan isi DB_*
php artisan key:generate
npm install && npm run build
php artisan storage:link
php artisan migrate --seed    # tabel + akun demo + kategori spare part
php artisan serve             # http://localhost:8000
```

Di terminal lain:

```bash
php artisan schedule:work     # pesanan & offer kedaluwarsa, pengingat servis
php artisan queue:work        # AI scan
```

`migrate --seed` **tidak** membuat mobil, spare part, atau riwayat transaksi.
Data demo lengkap dibuat dengan `php artisan demo:reset`, yang hanya bisa
dipakai bila katalog mobil dan spare part sudah terisi. Baca
[`docs/README.md`](docs/README.md) sebelum menjalankannya.
