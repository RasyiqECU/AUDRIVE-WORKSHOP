# AUDrive — Role, Hak Akses, Modul & Alur

Kondisi kode per **28 November 2026**. Sumber kebenaran: `routes/*.php`, Policy di
`app/Policies/`, dan sidebar `resources/views/components/layout/`. Panduan di
menu Manajemen Pengguna berasal dari `app/Services/RoleAccessMatrixService.php`.

## 1. Dua portal login

| Portal | URL | Untuk | Setelah login |
|---|---|---|---|
| Pelanggan | `/login` | `customer` | `/customer` |
| Internal | `/internal/login` (alias `/admin/login`) | staf & owner | `/admin` |

Masing-masing menolak akun dari portal lain.

## 2. Role

Delapan role, disimpan di kolom `users.role`. Tidak ada tabel role.

| Role | Ditampilkan sebagai | Fokus |
|---|---|---|
| `super-admin` | Super Admin | Semua modul, pengguna, dan konfigurasi |
| `owner` | Business Owner | Dashboard eksekutif dan laporan |
| `manager` | Manager | **Hanya** persetujuan, laporan, dan audit log |
| `sales` | Sales | Penjualan mobil, frontliner bengkel, CRM |
| `cashier` | Cashier | Kasir, verifikasi transfer, invoice |
| `inventory` | Inventory | Gudang, spare part, purchase order |
| `mechanic` | **Kepala Mekanik** | Work order, QC, diagnosa, inspeksi |
| `customer` | Customer | Portal pelanggan dan belanja |

## 3. Cara kerja hak akses

- Route dibatasi middleware `role:` (`app/Http/Middleware/RoleMiddleware.php`).
- Super Admin lolos semua pengecekan (juga lewat `Gate::before`).
- Sebagian aksi diperiksa lagi oleh Policy (misalnya Work Order).
- Akun nonaktif langsung dikeluarkan.
- Staf yang membuka halaman khusus customer dialihkan ke panel admin; pelanggaran
  lain menghasilkan 403.
- `PermissionMiddleware` terdaftar tetapi tidak dipakai route mana pun, dan
  `RolePermissionSeeder` tidak melakukan apa-apa bila Spatie tidak terpasang.

## 4. Matriks akses panel admin

`✔` = kelola, `👁` = lihat saja, kosong = tidak bisa. Super Admin bisa semuanya.

| Modul | Owner | Manager | Sales | Cashier | Inventory | Kep. Mekanik |
|---|---|---|---|---|---|---|
| Dashboard | ✔ | ✔ | ✔ | ✔ | ✔ | ✔ |
| Unit mobil (hapus: Super Admin) | | | ✔ | | | |
| Penawaran & test drive | | | ✔ | | | |
| Titip jual: lihat | | 👁 | 👁 | | | 👁 |
| Titip jual: inspeksi | | | | | | ✔ |
| Titip jual: setujui harga & komisi | | ✔ | | | | |
| Serah terima mobil | | | ✔ | | | |
| Booking servis | | | ✔ walk-in, check-in, status | ✔ status | | ✔ buat WO |
| Work order: lihat | | | 👁 | 👁 | | 👁 miliknya & belum ditugaskan |
| Work order: kerjakan, QC, tugaskan | | | | | | ✔ |
| Work order: serahkan kendaraan | | | ✔ | ✔ | | |
| Konfirmasi part ke customer | | | ✔ | | | |
| Cek usulan part | | | | | ✔ | |
| Klaim garansi | | ✔ putuskan | ✔ buat | | ✔ keluarkan part | ✔ diagnosa |
| AI scan (review) | | | | | | ✔ |
| POS & verifikasi transfer | | | | ✔ | | |
| Invoice | | | 👁 mobil | ✔ | | |
| Katalog spare part | | | | | ✔ | 👁 |
| Stok & purchase order | | | | | ✔ | |
| Siapkan pesanan | | | | | ✔ | |
| Database pelanggan (edit: Super Admin) | | | | 👁 tanpa menu | | |
| Loyalitas (koreksi poin: Super Admin) | | | 👁 tanpa menu | 👁 tanpa menu | | |
| Pesan kontak & tiket | | | ✔ | | | |
| Voucher & promo (ubah: Super Admin) | | | 👁 | | | |
| Ajukan approval | | | ✔ diskon | ✔ diskon, refund | ✔ harga, stok | |
| Putuskan approval | | ✔ | | | | |
| Audit log | | 👁 | | | | |
| Pengguna & konfigurasi sistem | | | | | | |

### Laporan

| Halaman | Owner | Manager | Sales | Cashier | Inventory |
|---|---|---|---|---|---|
| Ringkasan | semua | semua | penjualan yang ditanganinya | dialihkan ke Finansial | dialihkan ke Inventori |
| Penjualan | ✔ | ✔ | miliknya | | |
| Workshop | ✔ | ✔ | | | |
| Inventori | ✔ | ✔ | | | ✔ |
| Finansial | ✔ | ✔ | | Laporan Shift (uang yang diterimanya) | |

Owner membuka laporan lewat `/admin/reports` (tidak ada menu di sidebar-nya).
Kepala Mekanik tidak punya laporan.

## 5. Fitur

### Publik & pelanggan

| Area | Rute | Isi |
|---|---|---|
| Umum | `/`, `/about`, `/contact`, `/health` | Beranda, profil, form kontak |
| Marketplace | `/marketplace` | Katalog (filter baru/bekas, termasuk titip jual), detail, inspeksi |
| Beli mobil | `/marketplace/{slug}/booking`, `/offer`, `/test-drive` | Booking fee, penawaran, test drive |
| Titip jual | `/sell` | Ajukan mobil, pantau, tarik unit |
| Bengkel | `/workshop`, `/workshop/book`, `/workshop/my-bookings`, `/workshop/history` | Booking dan riwayat (booking khusus customer) |
| Tracking servis | `/workshop/tracking/{wo}` | Status, keputusan part, bayar online |
| Spare part | `/spare-parts`, `/cart`, `/checkout`, `/orders`, `/quote-request` | Belanja, bukti transfer, status pesanan |
| Portal | `/customer` | Kendaraan, invoice, AI scan, garansi, tiket, loyalitas, voucher, promo, pengingat, ulasan, notifikasi |
| Profil | `/profile` | Ubah data dan password (semua role) |

### REST API v1 (`/api/v1`, token Sanctum)

`health`, `register`, `login`, `logout`, `me`, `cars`, `test-drives`, `offers`,
`spare-parts`, `spare-part-categories`, `cart`, `checkout`, `orders/{id}`,
`orders/{id}/payment-proof`, `service-bookings`, `work-orders/{id}`,
`work-order-items/{id}/decision`, `ai-scans`, `customer/vehicles`,
`customer/loyalty`.

## 6. Alur

### Servis

Booking (customer atau Sales walk-in) → check-in (Sales) → work order + mekanik
(Kepala Mekanik) → pengerjaan → usulan part: cek gudang (Inventory) → konfirmasi
customer (Sales/customer) → QC → tagihan terbit → bayar (kasir atau transfer) →
serah terima (Sales/Cashier).

Pilihan customer per part: beli di bengkel (stok dipotong saat disetujui), bawa
sendiri (hanya jasa pasang, tanpa garansi part), atau tidak diganti.

### Klaim garansi

Servis 30 hari / 1.000 km; mobil milik AUDrive 6 bulan / 5.000 km sejak serah
terima. Mobil titip jual tidak digaransi AUDrive. Sales buat klaim → Kepala
Mekanik diagnosa → Manager putuskan → Inventory keluarkan part → perbaikan Rp 0
→ QC → serah terima.

### Mobil

- **Booking fee:** unit terkunci setelah fee diterima → pelunasan dalam batas hari
  → serah terima oleh Sales (ambil sendiri atau towing).
- **Penawaran:** Sales menerima → pesanan dibuat → pembayaran di kasir.
- **Titip jual:** customer ajukan → inspeksi Kepala Mekanik → Manager setujui
  harga, komisi, biaya servis → tayang → terjual → Cashier membayar penjual
  (harga − diskon − komisi − biaya servis).

### Pembayaran transfer (tanpa payment gateway)

Customer unggah bukti (JPG/PNG/WEBP, maks 2 MB, disimpan privat) → menunggu
verifikasi → Cashier approve (lunas, invoice, stok dipotong) atau reject dengan
alasan. Berlaku untuk spare part, tagihan servis, dan mobil.

### Stok

`stock` = fisik, `booked_stock` = dikunci pesanan, tersedia = selisihnya. Pesanan
yang lewat batas bayar kedaluwarsa dan stoknya kembali.

## 7. Aturan omzet

- Periode laporan mengikuti **tanggal lunas** (`sales_orders.paid_at`).
- Omzet (`revenue_idr`) = nilai transaksi. Khusus **mobil titip jual**: komisi +
  biaya servis (+ towing bila ada), bukan harga mobil.
- `sales_id` = Sales yang menangani: penerima penawaran atau yang check-in, lalu
  yang melakukan serah terima. Spare part tanpa Sales dikelompokkan
  "Penjualan Mandiri / Online"; mobil/servis yang belum ditangani
  "Belum Ditangani Sales".
- **Refund sebagian:** pesanan tetap lunas, omzet dikurangi di periode pelunasan
  aslinya. **Refund penuh:** status Dikembalikan, omzet nol, invoice dibatalkan.
  Stok, poin, dan status mobil tidak dikembalikan otomatis.
- **Laporan Shift Cashier** = uang yang diterima/diverifikasi (`payments.verified_by`),
  termasuk hak pemilik titip jual.
- **Pengeluaran** di dashboard Owner = barang purchase order yang sudah diterima.
  Pembelian unit mobil tidak tercatat, jadi pemasukan − pengeluaran bukan laba.
- Kolom omzet diisi otomatis oleh model `SalesOrder`; jangan mengubah status
  lunas lewat query builder.

## 8. Diskon & approval

Diskon hanya lewat pengajuan yang disetujui Manager (di kasir, hanya Super Admin
yang bisa mengisi diskon langsung). Pembuat pengajuan tidak bisa menyetujui
pengajuannya sendiri. POS tidak memiliki fitur voucher.
