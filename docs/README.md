# AUDrive — Panduan Menjalankan

Aplikasi manajemen dealership & workshop otomotif berbasis **Laravel 13**, dengan
diagnostik suara mesin sebagai microservice Python/FastAPI di `ai-service/`
(backend model: ONNX, transfer learning YAMNet). Root aplikasi = root repository.

Dokumen lain: [`ROLES_MODUL_FITUR.md`](ROLES_MODUL_FITUR.md),
[`AUDrive_Context.md`](AUDrive_Context.md), [`STRUCTURE.md`](STRUCTURE.md),
[`QA_CHECKLIST.md`](QA_CHECKLIST.md).

## Prasyarat

- PHP ≥ 8.4.1 (dikembangkan di 8.5)
- Composer 2
- Node.js dan npm (dikembangkan di Node 24)
- MySQL / MariaDB (dikembangkan dengan MariaDB dari XAMPP)
- Python 3.12 (hanya untuk AI service)

## Menjalankan Laravel

```bash
composer install
cp .env.example .env       # ubah DB_CONNECTION=mysql dan isi DB_*
php artisan key:generate
npm install
npm run build
php artisan storage:link   # wajib agar foto, audio, dan spectrogram tampil
php artisan migrate --seed # tabel + akun demo + kategori spare part
php artisan serve          # http://localhost:8000
```

Selama aplikasi dipakai, jalankan juga:

```bash
php artisan schedule:work  # orders:expire tiap 5 menit, offers:expire tiap jam,
                           # pengingat servis pukul 08.00
php artisan queue:work     # AI scan dan job lain (driver database)
npm run dev                # hanya saat mengubah tampilan
```

Tanpa `schedule:work`, pesanan yang lewat batas bayar tidak kedaluwarsa dan
stoknya tetap terkunci.

## Akun demo

Password semua akun: `password`.

| Login | Akun |
|---|---|
| `/internal/login` | `superadmin@gmail.com`, `manager@gmail.com`, `owner@gmail.com`, `mekanik1@gmail.com`, `mekanik2@gmail.com`, `sales@gmail.com`, `kasir@gmail.com`, `inventory@gmail.com` |
| `/login` | `customer1@gmail.com` s/d `customer5@gmail.com` |

Alamat `@gmail.com` bisa milik orang lain: biarkan `MAIL_MAILER=log`.

## Data demo

```bash
php artisan demo:reset     # minta konfirmasi; ditolak di production
```

- **Menghapus** semua akun, transaksi, riwayat, file AI scan, dan bukti transfer.
- **Menyimpan** katalog mobil, spare part, kategori, dan pengaturan. Unit titip
  jual dan unit arsip buatan seeder ikut dihapus lalu dibuat ulang.
- Membuat riwayat Januari 2025 sampai hari ini, ditambah contoh pekerjaan yang
  masih berjalan untuk pengetesan.
- Foto katalog diambil dari `public/images`.

> **Perhatian:** perintah ini mengandaikan katalog mobil (id 1–12) dan spare part
> sudah ada. Pada database kosong, seeder gagal **setelah** tabel dikosongkan.
> Backup database sebelum menjalankannya.

Untuk hanya mengisi voucher, promo, tiket, pesan kontak, dan purchase order yang
masih kosong:

```bash
php artisan db:seed --class=DemoSupportDataSeeder
```

## Pengaturan pembayaran

Login Super Admin → Konfigurasi Sistem → Pembayaran & Pengiriman: rekening
tujuan, ongkir, batas bayar, booking fee, batas pelunasan, biaya towing.
**Ganti nomor rekening contoh dengan rekening asli sebelum dipakai.**

## Menjalankan AI Service

```bash
cd ai-service
python -m venv .venv
.venv\Scripts\activate            # Linux/macOS: source .venv/bin/activate
pip install -r requirements.txt
cp .env.example .env              # set MODEL_BACKEND=onnx dan API_SECRET
uvicorn main:app --host 0.0.0.0 --port 8001 --reload
```

- Bawaan `.env.example` adalah `MODEL_BACKEND=dummy` (tanpa model). Untuk model
  asli gunakan `onnx`.
- `AI_SERVICE_SECRET` (Laravel) harus sama dengan `API_SECRET` (`ai-service/.env`).
- File model: lihat `ai-service/models/README.txt`.

## Menjalankan test

Test memakai database MySQL terpisah, `audrive_test`, yang dikosongkan setiap
dijalankan. Buat sekali: `CREATE DATABASE audrive_test;`

```bash
php -d memory_limit=1G vendor/bin/pest --compact
vendor/bin/pint --dirty    # format kode
```

`composer test` memakai batas memori bawaan PHP (128 MB) dan bisa kehabisan
memori. AI service belum memiliki test.

## Docker

Belum diimplementasikan: `docker/docker-compose.yml` hanya placeholder,
`Dockerfile` dan konfigurasi nginx masih kosong.

## Catatan

- Tidak dilacak Git: `.env`, `vendor/`, `node_modules/`, `.venv/`,
  `public/build`, `public/storage`, log, cache, `storage/app/public`,
  `storage/app/private`, dan bukti transfer.
- Setelah `git pull` yang membawa migrasi baru, jalankan `php artisan migrate`
  sebelum aplikasi dipakai.
