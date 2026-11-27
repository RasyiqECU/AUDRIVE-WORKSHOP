# AUDrive — Role, Hak Akses, Modul & Fitur

Rangkuman lengkap sistem role & hak akses (authorization) serta seluruh modul dan fitur yang ada di project AUDrive.

Dibuat berdasarkan kondisi kode terakhir: **11 November 2026**.
Sumber utama: `database/seeders/RolePermissionSeeder.php`, `app/Http/Middleware/RoleMiddleware.php`, `routes/*.php`.

---

## 1. Dua Portal Login yang Terpisah

| Portal | URL Login | Role | Halaman setelah login |
|---|---|---|---|
| **Portal Pelanggan (publik)** | `/login` | `customer` | Customer Dashboard (`/customer`) |
| **Portal Internal (tersembunyi)** | `/internal/login` (alias `/admin/login`) | Semua staf & owner | Admin Dashboard (`/admin`) |

Aturan penting:

- Halaman login utama `/login` **hanya menerima akun `customer`**. Akun staf/owner yang mencoba login di sini akan ditolak dengan pesan error.
- Halaman `/internal/login` **hanya menerima akun selain `customer`**. Customer yang mencoba masuk di sini akan ditolak.
- **Logout:**
  - Role internal (selain customer) logout → diarahkan kembali ke **form login internal** (`/internal/login`).
  - Customer logout → diarahkan ke **halaman utama** (`/`).
  - Logikanya ada di `App\Http\Controllers\Auth\LoginController::destroy()`.

---

## 2. Daftar Role

Terdapat **8 role**. Role disimpan di kolom `role` pada tabel `users`, dan juga disinkronkan ke **Spatie Laravel-Permission** (guard `web`).

| Role | Kategori | Deskripsi & Fokus Akses |
|---|---|---|
| `super-admin` | Internal | Super Admin. **Akses penuh ke seluruh modul** (di-bypass via `RoleMiddleware`, semua permission via Spatie). Satu-satunya role yang boleh: hapus unit mobil, hapus Work Order, kelola akun pengguna (role manajemen), dan konfigurasi sistem. |
| `owner` | Internal | Business Owner. Fokus **pantau performa bisnis**: dashboard, laporan penjualan/workshop/inventory/finance. Tidak ikut operasional harian. |
| `manager` | Internal | Manajemen. Hampir semua modul operasional: mobil, offer, appointment/test drive, booking servis, Work Order, review AI Scan, suku cadang, inventory, pembelian, POS, finance, laporan, approval, CRM, tiket, voucher/promo, loyalitas, audit log. Tidak boleh hapus unit mobil, tidak kelola akun pengguna, tidak ubah konfigurasi sistem. |
| `sales` | Internal | Sales showroom. Kelola **unit mobil & inspeksi**, kelola offer/negosiasi & jadwal test drive, balas pesan & tiket customer, lihat laporan penjualan & CRM. Tidak akses data CRM lengkap (edit), POS, finance, atau inventory. |
| `cashier` | Internal | Kasir / Frontdesk. Jalankan **POS**, kelola status booking servis & konversi ke Work Order, buat WO & proses pengiriman unit, kelola invoice/pembayaran, lihat data customer (CRM read-only), lihat laporan finance. |
| `inventory` | Internal | Staf Gudang/Inventory. Kelola **katalog suku cadang, stok & mutasi, dan Purchase Order** (buat, kirim ke supplier, terima barang, batalkan). Tidak akses penjualan/finance. |
| `mechanic` | Internal | Mekanik. Akses **Work Order** (update status, item servis/parts, selesaikan), **review hasil AI Scan suara mesin**, lihat katalog suku cadang & booking servis. Tidak akses penjualan/finance. |
| `customer` | Pelanggan | Pelanggan via portal publik: marketplace (test drive, offer/negosiasi, jual mobil ke dealer), booking servis & tracking, AI Scan, e-commerce suku cadang, kendaraan, tiket, loyalitas, voucher, invoice, review. |

Akun demo (password: `password`), dibuat lewat `php artisan demo:reset` (menghapus semua akun dan transaksi, katalog mobil dan spare part disimpan):
`superadmin@gmail.com`, `manager@gmail.com`, `owner@gmail.com`, `mekanik1@gmail.com`, `mekanik2@gmail.com`, `sales@gmail.com`, `kasir@gmail.com`, `inventory@gmail.com`, `customer1@gmail.com` s/d `customer5@gmail.com`.
Staf login di `/internal/login`, customer di `/login`.

---

## 3. Cara Kerja Sistem Hak Akses

### 3.1 Middleware Role

- Route admin diproteksi `middleware(['auth', 'role:super-admin|owner|manager|sales|cashier|inventory|mechanic'])`.
- Route customer diproteksi `middleware(['auth', 'role:customer'])`.
- `RoleMiddleware` mendukung daftar role dengan pemisah `|`, `,`, atau multi-argumen.
- **Super-admin selalu lolos** pengecekan role apa pun.
- Akun yang `is_active = false` otomatis di-logout saat mencoba akses.
- Jika staff/internal mencoba buka halaman khusus customer, dialihkan ke admin dashboard (mechanic → halaman workshop).
- Pelanggaran role → error **403** "Akses ditolak".

### 3.2 Permission (Spatie)

Sistem permission granular dikelola `RolePermissionSeeder` dengan pola `<modul>.<aksi>`:

| Kelompok | Permission |
|---|---|
| Cars | `cars.view / create / update / delete / inspect` |
| Offers | `offers.view / create / update / delete / manage` |
| Appointments | `appointments.view / create / update / delete / manage` |
| Test Drives | `test-drives.view / create / manage` |
| Service Bookings | `service-bookings.view / create / update / delete / manage` |
| Work Orders | `work-orders.view / create / update / delete / manage` |
| AI Scans | `ai-scans.view / create / review` |
| Spare Parts | `spare-parts.view / create / update / delete / manage` |
| Inventory | `inventory.view / manage` |
| Purchasing | `purchasing.view / create / update / manage` |
| POS | `pos.use` |
| Finance | `finance.view / manage` |
| Reports | `reports.view` |
| Users | `users.view / create / update / delete / manage` |
| Settings | `settings.view / manage` |
| Audit | `audit.view` |
| Approvals | `approvals.view / create / manage` |
| CRM | `crm.view / manage` |
| Tickets | `tickets.view / create / update / manage` |

> Proteksi route aktif saat ini berbasis `RoleMiddleware` (daftar role per route); permission Spatie tersedia untuk pemeriksaan granular tambahan (`PermissionMiddleware`).

---

## 4. Matriks Akses per Modul (Ringkas)

Legenda: ✅ = akses, 🔶 = akses terbatas/read-only, ❌ = tidak ada.

| Modul | Super Admin | Owner | Manager | Sales | Cashier | Inventory | Mechanic | Customer |
|---|---|---|---|---|---|---|---|---|
| Dashboard admin | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| Unit mobil (CRUD + inspeksi + foto) | ✅ penuh | ❌ | 🔶 tanpa hapus | 🔶 tanpa hapus | 🔶 lihat | ❌ | 🔶 lihat | 🔶 via marketplace |
| Offer / pesanan masuk | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ | 🔶 buat offer sendiri |
| Appointment & Test Drive | ✅ | ❌ | ✅ | ✅ | 🔶 lihat | ❌ | ❌ | 🔶 buat sendiri |
| POS / Kasir | ✅ | ❌ | 🔶 lihat menu | ❌ | ✅ | ❌ | ❌ | ❌ |
| Invoice & bukti bayar | ✅ | ❌ | ✅ | ✅ | ✅ | ❌ | ❌ | 🔶 miliknya |
| Booking servis (frontdesk) | ✅ | ❌ | 🔶 lihat | ❌ | ✅ kelola | ❌ | 🔶 lihat | 🔶 buat sendiri |
| Work Order & antrean bengkel | ✅ penuh | ❌ | 🔶 tanpa hapus, assign mekanik | ❌ | 🔶 buat, deliver, tanpa hapus | ❌ | 🔶 kerjakan/update | 🔶 tracking miliknya |
| AI Scan (review & verdict) | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ | ✅ | 🔶 buat & lihat miliknya |
| Katalog suku cadang (CRUD) | ✅ | ❌ | 🔶 lihat | 🔶 lihat | 🔶 lihat | ✅ | 🔶 lihat | 🔶 belanja |
| Inventory stok & mutasi | ✅ | ❌ | 🔶 lihat, tanpa ubah | ❌ | ❌ | ✅ | ❌ | ❌ |
| Purchase Order | ✅ | ❌ | 🔶 lihat | ❌ | ❌ | ✅ | ❌ | ❌ |
| CRM / database pelanggan | ✅ | ❌ | ✅ | ❌ | 🔶 read-only | ❌ | ❌ | ❌ |
| Pesan masuk & balasan | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ | 🔶 kirim |
| Tiket layanan/komplain | ✅ | ❌ | ✅ | 🔶 view & balas | ❌ | ❌ | ❌ | 🔶 buat & balas |
| Voucher & promo | ✅ | ❌ | ✅ | 🔶 lihat | ❌ | ❌ | ❌ | 🔶 klaim |
| Program loyalitas | ✅ | ❌ | ✅ | 🔶 lihat | 🔶 lihat | ❌ | ❌ | 🔶 redeem |
| Laporan & analitik | ✅ semua | ✅ semua | ✅ semua | 🔶 sales | 🔶 finance | 🔶 inventory | ❌ | ❌ |
| Approval request | ✅ | ❌ | ✅ (pemberi keputusan) | 🔶 ajukan | 🔶 ajukan | 🔶 ajukan | ❌ | ❌ |
| Audit log | ✅ | ❌ | 🔶 lihat | ❌ | ❌ | ❌ | ❌ | ❌ |
| Manajemen pengguna & role | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Konfigurasi sistem | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Portal customer (`/customer`) | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |

---

## 5. Modul & Fitur

### 5.1 Situs Publik (tanpa login)

| Fitur | Rute | Penjelasan |
|---|---|---|
| Beranda | `/` | Landing page AUDrive |
| Tentang Kami | `/about` | Halaman profil perusahaan |
| Kontak | `/contact` + `POST` | Form pesan masuk customer (masuk ke modul Contact Messages admin) |
| Health check | `/health` | Endpoint monitoring JSON (status, versi, environment) |

### 5.2 Marketplace Mobil (publik + login)

| Fitur | Rute | Penjelasan |
|---|---|---|
| Katalog & pencarian mobil | `/marketplace`, `/marketplace/search` | Daftar unit supercar, filter & pencarian |
| Detail unit | `/marketplace/{slug}` | Spesifikasi & foto per unit |
| Laporan inspeksi unit | `/marketplace/{slug}/inspection` | Hasil inspeksi quality control unit |
| Booking test drive | `/marketplace/{slug}/test-drive` | (login) ajukan jadwal test drive |
| Offer / negosiasi harga | `/marketplace/{slug}/offer`, `/marketplace/offers` | (login) kirim, ubah, batalkan tawaran |
| Jual mobil ke dealer | `/sell` | (login) ajukan jual mobil ke AUDrive |

### 5.3 Workshop / Bengkel (publik + login)

| Fitur | Rute | Penjelasan |
|---|---|---|
| Info workshop | `/workshop` | Halaman publik layanan bengkel |
| Booking servis | `/workshop/book`, `/workshop/my-bookings` | (login) buat booking, lihat daftar booking miliknya + kwitansi |
| Tracking Work Order | `/workshop/tracking/{wo}`, `.../timeline` | (login) pantau status pengerjaan + timeline riwayat |
| Riwayat servis | `/workshop/history` | (login) riwayat servis kendaraan |
| AI Scan (sisi customer/workshop) | `/workshop/ai-scan` | (login) upload rekaman suara mesin untuk diagnosis AI |

### 5.4 E-commerce Suku Cadang (publik + login)

| Fitur | Rute | Penjelasan |
|---|---|---|
| Katalog & kategori parts | `/spare-parts`, `.../categories/{slug}` | Katalog publik, pencarian, per kategori |
| Kompatibilitas parts | `/spare-parts/{slug}/compatibility` | Cek kecocokan parts dengan mobil |
| Keranjang | `/cart` | Tambah/ubah/hapus item keranjang |
| Checkout & pembayaran | `/checkout`, `/checkout/payment/{order}` | Proses pesanan + pembayaran |
| Pesanan & invoice | `/orders` | Riwayat pesanan + invoice |
| Quote request (parts langka) | `/quote-request` | Ajukan permintaan kutipan untuk parts yang tidak stok |

### 5.5 Portal Customer (`/customer`, khusus role `customer`)

| Fitur | Rute | Penjelasan |
|---|---|---|
| Dashboard | `/customer` | Ringkasan aktivitas pelanggan |
| AI Scan suara mesin | `/customer/ai-scan` | Upload rekaman suara mesin → hasil diagnosis AI (proses via queue + service Python) |
| Kendaraan saya | `/customer/vehicles` | Kelola daftar kendaraan milik pelanggan |
| Tiket dukungan | `/customer/tickets` | Buat tiket komplain/bantuan + balas |
| Program loyalitas | `/customer/loyalty` | Salinan poin, riwayat, redeem reward |
| Pengingat | `/customer/reminders` | Reminder servis (bisa dismiss) |
| Ulasan | `/customer/reviews` | Buat & lihat review/rating |
| Voucher & promo | `/customer/vouchers`, `/customer/promotions` | Klaim voucher, lihat promo berjalan |
| Notifikasi | `/customer/notifications` | Notifikasi in-app (tandai baca / baca semua) |
| Invoice | `/customer/invoices` | Invoice miliknya (PDF / cetak) |

### 5.6 Panel Admin (`/admin`, internal staff)

| Fitur | Rute | Role | Penjelasan |
|---|---|---|---|
| Dashboard & notifikasi | `/admin` | semua internal | Ringkasan KPI, notifikasi in-app |
| Unit mobil | `/admin/cars` | SA, Mgr, Sales | CRUD unit supercar, inspeksi QC, foto (primary/replace), status, unit unggulan (featured) |
| Offer / pesanan masuk | `/admin/offers` | SA, Mgr, Sales | Review offer customer, terima (`accept`) / tolak (`reject`) |
| Appointment & test drive | `/admin/appointments` | SA, Mgr, Sales | Kelola jadwal & status test drive |
| POS (kasir) | `/admin/pos` | SA, Cashier | Transaksi kasir, bayar WO & penjualan mobil, struk (print) |
| Invoice | `/admin/invoices` | SA, Mgr, Cashier, Sales | Daftar invoice, bukti pembayaran, PDF, cetak |
| Booking servis | `/admin/service-bookings` | SA, Mgr, Cashier | Frontdesk: kelola status booking, konversi booking → Work Order |
| Work Order & antrean | `/admin/work-orders` | SA, Mgr, Cashier, Mechanic | Buat WO (SA/Cashier), assign mekanik (SA/Mgr), update status & item servis/parts, complete, deliver, hapus (SA) |
| AI Scan review | `/admin/ai-scans` | SA, Mgr, Mechanic | Review hasil diagnosis AI, beri verdict/kesimpulan akhir |
| Katalog suku cadang | `/admin/spare-parts` | SA, Mgr, Inv, Mechanic | CRUD parts (SA/Inventory), sisanya read-only |
| Inventory | `/admin/inventory` | SA, Mgr, Inv | Stok, low-stock alert, mutasi barang, adjust stok (SA/Inv) |
| Purchase Order | `/admin/purchase-orders` | SA, Mgr, Inv | Buat PO, kirim ke supplier, terima barang, batalkan |
| CRM pelanggan | `/admin/customers` | SA, Mgr, Cashier | Database pelanggan, kendaraan & riwayat servis; edit data (SA/Mgr) |
| Pesan masuk | `/admin/contact-messages` | SA, Mgr, Sales | Baca & balas pesan dari form kontak |
| Tiket layanan | `/admin/tickets` | SA, Mgr, Sales | Kelola tiket customer: balas, ubah status |
| Voucher & promo | `/admin/vouchers`, `/admin/promotions` | SA, Mgr (Sales lihat) | CRUD voucher/promo + aktif/nonaktif |
| Loyalitas | `/admin/loyalty` | SA, Mgr, Sales, Cashier | Monitor poin & history; penyesuaian poin manual (SA/Mgr) |
| Laporan | `/admin/reports` | semua kecuali Mechanic | Laporan sales (SA/Owner/Mgr/Sales), workshop (SA/Owner/Mgr), inventory (SA/Owner/Mgr/Inv), finance (SA/Owner/Mgr/Cashier) |
| Approval | `/admin/approvals` | SA, Mgr (Sales/Cashier/Inv ajukan) | Alur persetujuan pengajuan staf operasional (approve/reject) |
| Audit log | `/admin/audit-logs` | SA, Mgr | Jejak aktivitas pengguna |
| Manajemen pengguna | `/admin/users` | **SA saja** | CRUD akun + pengaturan role & status aktif |
| Pengaturan sistem | `/admin/settings` | **SA saja** | Konfigurasi sistem + reset |

> Keterangan role: SA = super-admin, Mgr = manager, Inv = inventory, Cashier = cashier.

### 5.7 REST API v1 (`/api/v1`, Sanctum token)

| Kelompok | Endpoint | Penjelasan |
|---|---|---|
| Health | `GET /api/v1/health` | Status API |
| Auth | `POST /register`, `POST /login`, `POST /logout`, `GET /me` | Registrasi & login via token |
| Marketplace | `GET /cars`, `GET /cars/{id}` + `POST /test-drives`, `POST /offers`, `GET /offers` | Data mobil & aksi pelanggan |
| Spare parts | `GET /spare-parts`, `GET /spare-parts/{id}`, `GET /spare-part-categories` | Katalog parts |
| Workshop | `GET|POST /service-bookings`, `GET /service-bookings/{id}`, `GET /work-orders/{id}` | Booking & tracking |
| AI Scans | `GET|POST /ai-scans`, `GET /ai-scans/{id}` | Diagnosis AI via API |
| Cart | `GET /cart`, CRUD `/cart/items`, `POST /checkout` | Belanja parts via API |
| Customer | `GET|POST /customer/vehicles`, `GET /customer/loyalty` | Data kendaraan & loyalitas |

Semua API throttled `30 req/menit` (public) kecuali route auth.

### 5.8 AI Service (Python — `ai-service/`)

Microservice terpisah (FastAPI, port 8001) yang dipanggil Laravel via HTTP dengan header secret `X-Api-Secret`:

| Endpoint | Fungsi |
|---|---|
| `POST /predict` | Diagnosis suara mesin (MFCC/Mel-spectrogram → model XGBoost/ONNX/PyTorch/Keras) |
| `GET /spectrogram/{file}` | Sajikan file spectrogram hasil analisis |
| `GET /health` | Health check service |
| `GET /classes` | Daftar kelas diagnosis yang dikenali model |

Alur: customer/staf upload rekaman → Laravel simpan & dispatch `ProcessAiScanJob` (queue `database`) → `AiScanService` kirim audio ke service Python → hasil diagnosis disimpan → mekanik/manager memberi verdict akhir di panel admin → hasil tampil di portal customer.

---

## 6. Alur Servis, Garansi, dan Titip Jual (berlaku sejak 27 November 2026)

Bagian ini menggantikan keterangan lama pada tabel di atas bila berbeda. Tidak ada role baru. Role `mechanic` ditampilkan sebagai **Kepala Mekanik**.

### 6.1 Perubahan hak akses

| Role | Tambahan | Dicabut |
|---|---|---|
| Sales | Booking walk-in, check-in, konfirmasi part, buat klaim garansi, serah terima, setujui titip jual | — |
| Manager | Putuskan klaim garansi (hanya approval, laporan, audit log) | Semua akses operasional: mobil, offer, test drive, booking, WO, AI scan, gudang, PO, CRM, tiket, voucher/promo, loyalitas, invoice |
| Mechanic (Kepala Mekanik) | Usul part, diagnosa klaim, lihat Booking Servis, buat WO, tugaskan, QC | Tambah part langsung, menandai selesai |
| Inventory | Cek usulan part, keluarkan part garansi | — |
| Cashier | Lunasi pesanan online, bayar penjual titip jual | Konversi booking ke WO, buat WO |

Edit voucher/promo, data pelanggan, dan koreksi poin sekarang hanya Super Admin.

Semua role punya halaman `/profile` untuk mengubah data dan password.

### 6.2 Alur servis

Booking (customer / Sales) → check-in (Sales) → WO + mekanik + estimasi (Kepala Mekanik) → pengerjaan (Mekanik) → usulan part: cek gudang (Inventory) → konfirmasi WhatsApp (Sales) → QC (Kepala Mekanik) → bayar (Cashier) → serah terima (Sales / Cashier).

Pilihan customer per part: beli di bengkel (stok dipotong saat disetujui), bawa sendiri (hanya jasa pasang, tanpa garansi part), atau tidak diganti.

### 6.3 Klaim garansi

Servis 30 hari / 1.000 km; mobil milik AUDrive 6 bulan / 5.000 km sejak serah terima. Sales buat klaim → Mekanik diagnosa → Manager setuju/tolak → Inventory keluarkan part (mutasi `warranty`) → Mekanik pasang → QC → nota Rp 0 (Cashier) → serah terima. Customer memantau di `/customer/warranty`.

### 6.4 Titip jual

Customer ajukan di `/sell` → Sales inspeksi, tetapkan harga minimum, harga tayang, komisi → tayang "Bekas - Titip Jual" → dibeli seperti mobil lain → Cashier mencatat pembayaran ke penjual. AUDrive tidak memberi garansi pada unit titip jual.

Offer kedaluwarsa dikembalikan otomatis tiap jam — butuh `php artisan schedule:work` (atau cron `schedule:run`).

---

## 7. Pembayaran Online, Stok Booking, dan Titip Jual (27 November 2026)

### 7.1 Pembayaran transfer (tanpa payment gateway)

Customer unggah bukti (JPG/PNG/WEBP, maks 2 MB, disimpan privat di `storage/app/payment-proofs`) → status `waiting_verification` → Cashier **Approve** (lunas, invoice, potong stok) atau **Reject** dengan alasan (unggah ulang). Berlaku untuk pesanan spare part, tagihan servis, dan booking/pelunasan mobil. Satu tabel `payments` polimorfik ke `sales_orders`.

### 7.2 Stok

`spare_parts.stock` = stok fisik, `booked_stock` = dikunci pesanan, tersedia = selisihnya. Pesanan yang lewat batas bayar kedaluwarsa dan stoknya kembali (jadwal `orders:expire` tiap 5 menit — butuh `php artisan schedule:work`).

### 7.3 Mobil

Booking fee mengunci unit (`reserved`) → pelunasan dalam batas hari → serah terima oleh Sales (ambil sendiri / towing). Titip jual: inspeksi Kepala Mekanik → persetujuan harga, komisi, biaya servis oleh Manager → dipasarkan Sales → Cashier membayar penjual (harga − komisi − biaya servis).

### 7.4 Pengaturan

Konfigurasi Sistem → Pembayaran & Pengiriman: rekening tujuan, ongkir, batas bayar, booking fee, batas pelunasan, towing.

### 7.5 API customer

`POST /api/v1/checkout` (`payment_channel`), `GET /api/v1/orders/{id}`, `POST /api/v1/orders/{id}/payment-proof`, `POST /api/v1/work-order-items/{id}/decision`.

---

## 8. Catatan Teknis Lain

- Fitur AI Scan aktif lewat route di `admin.php` (`/admin/ai-scans`) dan `customer.php` (`/customer/ai-scan`).
- Notifikasi in-app tersedia di kedua portal (list, tandai satu baca, tandai semua baca).
- Semua route modular dipisah per file di folder `routes/` dan dimuat dari `routes/web.php`.

---

## 8a. Aturan Omzet (28 November 2026)

- Periode laporan mengikuti tanggal lunas (`sales_orders.paid_at`), bukan tanggal pesanan.
- Omzet (`revenue_idr`) = nilai transaksi. Khusus mobil titip jual: komisi + biaya servis (+ towing bila ada), bukan harga mobil.
- `sales_id` = Sales yang menangani: penerima penawaran, yang check-in, lalu yang melakukan serah terima. Spare part tanpa Sales masuk "Penjualan Mandiri / Online"; mobil/servis yang belum ditangani masuk "Belum Ditangani Sales".
- Refund sebagian: pesanan tetap lunas, omzet dikurangi (`refunded_idr`). Refund penuh: status Dikembalikan, omzet nol, invoice dibatalkan.
- Laporan Shift Cashier dihitung dari pembayaran yang diterima/diverifikasinya (`payments.verified_by`).
- Kolom diisi otomatis oleh model `SalesOrder`; jangan mengubah status lunas lewat query builder.
