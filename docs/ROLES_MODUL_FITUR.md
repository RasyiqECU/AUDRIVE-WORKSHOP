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

Akun demo (setelah `db:seed`, password: `password`):
`admin@audrive.local`, `owner@audrive.local`, `manager@audrive.local`, `sales@audrive.local`, `mechanic@audrive.local`, `cashier@audrive.local`, `inventory@audrive.local`, `customer@audrive.local`.

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

## 6. Catatan Teknis Lain

- File `routes/admin-ai-scan.php` dan `routes/customer-ai-scan.php` saat ini **tidak dimuat** oleh `web.php` (versi legacy); fitur AI Scan aktif lewat route di `admin.php` (`/admin/ai-scans`) dan `customer.php` (`/customer/ai-scan`).
- Notifikasi in-app tersedia di kedua portal (list, tandai satu baca, tandai semua baca).
- Semua route modular dipisah per file di folder `routes/` dan dimuat dari `routes/web.php`.
