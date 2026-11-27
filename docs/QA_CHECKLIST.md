# AUDrive — QA Checklist

Status per **28 November 2026**. ✔ = terverifikasi, ☐ = belum ada atau belum dilakukan.

## Test suite

- **256 test: 253 lulus, 3 dilewati, 2.324 assertion.**
- Perintah: `php -d memory_limit=1G vendor/bin/pest --compact`
- Database `audrive_test` (MySQL), dikosongkan tiap dijalankan. Jangan memakai
  database ini untuk keperluan lain saat test berjalan.

## Sudah tercakup

- ✔ Akses route per role dan menu sidebar tidak menuju halaman terlarang
- ✔ Panduan hak akses sama dengan jumlah menu sidebar
- ✔ Login terpisah, rate limit, CSRF, validasi dan keamanan unggahan
- ✔ Pembayaran transfer, stok terkunci, pesanan kedaluwarsa
- ✔ Servis, konfirmasi part, klaim garansi, titip jual, booking mobil
- ✔ Approval diskon, refund sebagian dan penuh
- ✔ Aturan omzet, rekap per Sales, Laporan Shift, angka dashboard Owner

## Masalah yang diketahui

- ☐ **Email notifikasi tidak terkirim:** kelas di `app/Mail/` belum dibuat.
  Notifikasi di dalam aplikasi berjalan.
- ☐ **`composer audit`:** 15 advisory pada 7 package (Laravel & Symfony); belum
  diperbarui.
- ☐ **Nomor WhatsApp berbeda** di halaman mobil, spare part, dan kontak.
- ☐ **`demo:reset` gagal pada database tanpa katalog** mobil dan spare part,
  setelah tabel terlanjur dikosongkan.
- ☐ Refund penuh tidak mengembalikan stok, poin loyalitas, atau status mobil.
- ☐ Kode yang tidak dipakai: `FinanceService`/`FinanceController` (belum mengikuti
  aturan omzet) dan driver payment gateway.
- ☐ Tampilan dan interaksi di browser belum diuji otomatis.
- ☐ `APP_DEBUG=true` di contoh environment; wajib `false` di production.

## Belum ada

- ☐ CI/CD, PHPStan, laporan coverage
- ☐ Dokumentasi API (OpenAPI) dan skema database
- ☐ Test untuk AI service
- ☐ Docker

## Sebelum deploy

- ☐ `APP_ENV=production`, `APP_DEBUG=false`, HTTPS
- ☐ Perbarui dependency (`composer audit` bersih)
- ☐ `php artisan migrate --force` (jangan jalankan seeder demo)
- ☐ `php artisan storage:link`
- ☐ Cron `schedule:run` dan supervisor untuk `queue:work`
- ☐ Rekening tujuan asli di Konfigurasi Sistem
- ☐ Ganti semua password akun demo
- ☐ Backup database dan `storage/app`
