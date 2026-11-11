<?php

declare(strict_types=1);

namespace App\Services;

class RoleAccessMatrixService
{
    /**
     * Matriks lengkap hak akses seluruh role sistem AUDrive.
     *
     * Keterangan di bawah ini mencerminkan akses AKTUAL yang ditegakkan oleh
     * middleware `role:` pada routes/admin.php, Policy, dan visibilitas menu
     * sidebar (admin-sidebar.blade.php). Daftar `menus` pada setiap role adalah
     * item yang BENAR-BENAR tampil di sidebar role tersebut — bukan daftar
     * kemampuan ideal. Perbarui bagian ini setiap kali hak akses role diubah
     * agar panduan di menu Manajemen Pengguna selalu akurat.
     *
     * Catatan umum: TIDAK ada menu "Notifikasi" di sidebar. Notifikasi hanya
     * diakses lewat ikon lonceng 🔔 di topbar (halaman admin/notifications),
     * dan isinya sudah difilter per-role oleh NotificationService.
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $matrix = [
        'super-admin' => [
            'key' => 'super-admin',
            'name' => 'Super Administrator',
            'category' => 'Sistem & Core Authority',
            'badge_class' => 'bg-giallo/20 border-giallo text-giallo',
            'icon' => '👑',
            'summary' => 'Pemegang otoritas tertinggi sistem dengan akses penuh ke seluruh 22 menu sidebar: semua modul showroom, workshop, kasir, gudang, CRM, tata kelola, manajemen pengguna, dan konfigurasi sistem.',
            'menus' => [
                [
                    'name' => 'Dashboard (Dashboard Executive)',
                    'icon' => '⬡',
                    'route_hint' => 'admin.dashboard',
                    'access_level' => 'Full Access',
                    'description' => 'Ringkasan seluruh KPI: omzet, antrean workshop, penjualan showroom, inventori, dan alert sistem.',
                ],
                [
                    'name' => 'Unit Supercar (Showroom & Penjualan)',
                    'icon' => '🏎',
                    'route_hint' => 'admin.cars.*',
                    'access_level' => 'CRUD + Hapus Permanen',
                    'description' => 'Tambah, edit, unggah foto, set featured, inspeksi fisik, sampai menghapus permanen unit mobil.',
                ],
                [
                    'name' => 'Pesanan Masuk (Showroom & Penjualan)',
                    'icon' => '📋',
                    'route_hint' => 'admin.offers.*',
                    'access_level' => 'Full Control',
                    'description' => 'Melihat, menerima (accept), dan menolak penawaran pembelian mobil dari website.',
                ],
                [
                    'name' => 'Jadwal Test Drive (Showroom & Penjualan)',
                    'icon' => '🏁',
                    'route_hint' => 'admin.appointments.*',
                    'access_level' => 'Full Control',
                    'description' => 'Melihat dan mengubah status janji temu test drive (confirm/completed/missed).',
                ],
                [
                    'name' => 'Voucher & Promo (Showroom & Penjualan)',
                    'icon' => '🎟️',
                    'route_hint' => 'admin.vouchers.*, admin.promotions.*',
                    'access_level' => 'CRUD + Aktif/Nonaktif',
                    'description' => 'Membuat, mengedit, mengaktifkan/menonaktifkan voucher diskon dan program promo.',
                ],
                [
                    'name' => 'Booking Servis (Workshop & Servis)',
                    'icon' => '📅',
                    'route_hint' => 'admin.service-bookings.*',
                    'access_level' => 'Full Control',
                    'description' => 'Konfirmasi booking online, ubah status, kembalikan ke antrian, dan konversi menjadi Work Order.',
                ],
                [
                    'name' => 'Work Order & Antrean (Workshop & Servis)',
                    'icon' => '🔧',
                    'route_hint' => 'admin.work-orders.*',
                    'access_level' => 'Full Control',
                    'description' => 'Assign teknisi, tambah item jasa/part, ubah status pengerjaan, selesaikan, serahkan unit, dan hapus WO.',
                ],
                [
                    'name' => 'AI Sound Scan (Workshop & Servis)',
                    'icon' => '🔊',
                    'route_hint' => 'admin.ai-scans.*',
                    'access_level' => 'Full Review',
                    'description' => 'Melihat dan mereview semua hasil diagnosis suara mesin dari AUDrive Intelligence.',
                ],
                [
                    'name' => 'POS Kasir (Point of Sale)',
                    'icon' => '💳',
                    'route_hint' => 'admin.pos.*',
                    'access_level' => 'Full Control Transaksi',
                    'description' => 'Memproses semua pembayaran (WO, pembelian mobil, ritel part), cetak struk thermal, dan minta pembayaran unit terjadwal.',
                ],
                [
                    'name' => 'Invoice & Bukti Bayar (Point of Sale)',
                    'icon' => '🧾',
                    'route_hint' => 'admin.invoices.*',
                    'access_level' => 'Full Control',
                    'description' => 'Membuka, mencetak, dan mengunduh PDF seluruh invoice transaksi.',
                ],
                [
                    'name' => 'Katalog Spare Part (Gudang & Inventori)',
                    'icon' => '⚙️',
                    'route_hint' => 'admin.spare-parts.*',
                    'access_level' => 'CRUD + Hapus',
                    'description' => 'Mengelola master data SKU suku cadang beserta foto dan harga jual.',
                ],
                [
                    'name' => 'Stok & Movement (Gudang & Inventori)',
                    'icon' => '📦',
                    'route_hint' => 'admin.inventory.*',
                    'access_level' => 'Full Control',
                    'description' => 'Melihat kartu stok, daftar stok menipis, edit data part, dan penyesuaian stok (adjust stock).',
                ],
                [
                    'name' => 'Purchase Order (Gudang & Inventori)',
                    'icon' => '🛒',
                    'route_hint' => 'admin.purchase-orders.*',
                    'access_level' => 'Full Control',
                    'description' => 'Membuat, mengirim, menerima barang, dan membatalkan seluruh PO pengadaan.',
                ],
                [
                    'name' => 'Pesan Customer (CRM & Komunikasi)',
                    'icon' => '📬',
                    'route_hint' => 'admin.contact-messages.*',
                    'access_level' => 'Full Control',
                    'description' => 'Membaca, membalas, dan menghapus pesan konsultasi dari website.',
                ],
                [
                    'name' => 'Tiket Layanan (CRM & Komunikasi)',
                    'icon' => '🎫',
                    'route_hint' => 'admin.tickets.*',
                    'access_level' => 'Full Control',
                    'description' => 'Memproses dan membalas tiket bantuan/komplain customer.',
                ],
                [
                    'name' => 'Program Loyalitas (CRM & Komunikasi)',
                    'icon' => '👑',
                    'route_hint' => 'admin.loyalty.*',
                    'access_level' => 'Full Control',
                    'description' => 'Melihat saldo & riwayat poin member dan melakukan koreksi poin loyalitas.',
                ],
                [
                    'name' => 'Database Pelanggan (CRM & Komunikasi)',
                    'icon' => '👥',
                    'route_hint' => 'admin.customers.*',
                    'access_level' => 'Full Control',
                    'description' => 'Melihat dan mengkoreksi data pelanggan, garasi kendaraan, dan riwayat transaksinya.',
                ],
                [
                    'name' => 'Laporan Performa (Laporan)',
                    'icon' => '📊',
                    'route_hint' => 'admin.reports.*',
                    'access_level' => 'Semua Divisi',
                    'description' => 'Akses laporan Penjualan Supercar, Workshop, Inventori, dan Finansial Kasir.',
                ],
                [
                    'name' => 'Pusat Approval (Tata Kelola)',
                    'icon' => '✅',
                    'route_hint' => 'admin.approvals.*',
                    'access_level' => 'Approve / Reject',
                    'description' => 'Menyetujui/menolak pengajuan diskon, refund, koreksi harga, dan penyesuaian stok dari staf.',
                ],
                [
                    'name' => 'Audit Trail (Tata Kelola)',
                    'icon' => '📋',
                    'route_hint' => 'admin.audit-logs.*',
                    'access_level' => 'Full Filter',
                    'description' => 'Menelusuri seluruh jejak perubahan data dan keputusan pengguna sistem.',
                ],
                [
                    'name' => 'Manajemen Pengguna (Super Admin Core)',
                    'icon' => '🔑',
                    'route_hint' => 'admin.users.*',
                    'access_level' => 'Eksklusif Super Admin',
                    'description' => 'Menambah, mengedit, mengubah role, dan menghapus akun pengguna portal.',
                ],
                [
                    'name' => 'Konfigurasi Sistem (Super Admin Core)',
                    'icon' => '⚙',
                    'route_hint' => 'admin.settings.*',
                    'access_level' => 'Eksklusif Super Admin',
                    'description' => 'Mengubah nama portal, logo, dan pengaturan aplikasi lainnya (termasuk reset).',
                ],
            ],
            'allowed_actions' => [
                'Bypass seluruh otorisasi sistem via Master Gate (Gate::before)',
                'Membuat, memperbarui, dan menghapus permanen unit supercar dan spare part',
                'Mengelola akun semua pengguna portal dan menetapkan role hak akses',
                'Mengubah pengaturan dan variabel lingkungan portal AUDrive',
                'Menyetujui (Approve) atau menolak (Reject) semua pengajuan approval dan mengeksekusi efeknya',
                'Menghapus Work Order dan data master sistem lainnya',
            ],
            'restricted_actions' => [
                'Tidak ada batasan teknis (memiliki tanggung jawab etis penuh atas integritas data sistem)',
            ],
        ],

        'owner' => [
            'key' => 'owner',
            'name' => 'Business Owner',
            'category' => 'Eksekutif & Pemilik Usaha',
            'badge_class' => 'bg-amber-400/20 border-amber-400 text-amber-400',
            'icon' => '👔',
            'summary' => 'Pemilik usaha dengan hak akses hanya 1 menu: Executive Dashboard. Seluruh pelaporan finansial, omzet, margin, dan performa divisi tampil di dalam halaman dashboard eksekutif ini — tidak ada menu Laporan maupun menu operasional lain di sidebar.',
            'menus' => [
                [
                    'name' => 'Executive Dashboard',
                    'icon' => '⬡',
                    'route_hint' => 'admin.dashboard',
                    'access_level' => 'Monitoring Real-Time (Menu Tunggal)',
                    'description' => 'Satu-satunya halaman Owner: grafik omzet bersih, margin laba, rasio showroom vs bengkel vs ritel part, filter periode (Hari Ini/Bulan/Tahun/Custom), dan daftar acara terkini. Data pelaporan yang dibutuhkan sudah diintegrasikan di sini.',
                ],
            ],
            'allowed_actions' => [
                'Melihat metrik finansial, omzet harian/bulanan/tahunan, dan rasio laba usaha',
                'Menggunakan filter tanggal dinamis (Hari Ini, Minggu Ini, Bulan Ini, Tahun Ini, Custom Periode)',
                'Menerima notifikasi khusus via ikon 🔔 di topbar bila ada tipe notifikasi yang menyasar owner',
            ],
            'restricted_actions' => [
                'Dilarang menambah, mengedit, atau menghapus data transaksi maupun katalog apapun',
                'TIDAK ada menu operasional apapun di sidebar Owner (satu-satunya menu adalah Executive Dashboard)',
                'TIDAK ada menu Laporan di sidebar — pelaporan telah terangkum di dalam Executive Dashboard',
                'Dilarang mengakses mesin kasir (POS), Work Order, Database Pelanggan, Unit Supercar, maupun Gudang',
                'Dilarang mengakses menu Manajemen Pengguna, Konfigurasi Sistem, dan Pusat Approval',
                'Notifikasi hanya via ikon 🔔 topbar (tidak ada halaman operasional lain yang bisa dibuka)',
            ],
        ],

        'manager' => [
            'key' => 'manager',
            'name' => 'Operational Manager',
            'category' => 'Manajerial & Pengawas Operasional',
            'badge_class' => 'bg-blue-400/20 border-blue-400 text-blue-400',
            'icon' => '🧭',
            'summary' => 'Role pengawas & pemberi persetujuan dengan hanya 5 menu sidebar: Dashboard, Work Order & Antrean, Laporan Performa, Pusat Approval, dan Audit Trail. Fokusnya menugaskan teknisi, menyetujui pengajuan staf, dan membaca laporan — bukan kerja teknis/kasir/frontdesk.',
            'menus' => [
                [
                    'name' => 'Dashboard (Dashboard Manager)',
                    'icon' => '⬡',
                    'route_hint' => 'admin.dashboard',
                    'access_level' => 'Monitoring Operasional',
                    'description' => 'Ikhtisar antrean bengkel, showroom, dan notifikasi pengajuan approval yang menunggu keputusan.',
                ],
                [
                    'name' => 'Work Order & Antrean (Workshop & Servis)',
                    'icon' => '🔧',
                    'route_hint' => 'admin.work-orders.*',
                    'access_level' => 'View + Penugasan Mekanik',
                    'description' => 'Memantau antrean WO dan menugaskan (assign) teknisi. Tidak bisa mengubah rincian teknis/status, membuat WO, maupun menyerahkan unit — itu wewenang mekanik ter-assign dan Kasir.',
                ],
                [
                    'name' => 'Laporan Performa (Laporan)',
                    'icon' => '📊',
                    'route_hint' => 'admin.reports.*',
                    'access_level' => 'Semua Divisi',
                    'description' => 'Membaca laporan Penjualan Supercar, Workshop, Inventori, dan Finansial Kasir untuk pengawasan.',
                ],
                [
                    'name' => 'Pusat Approval (Tata Kelola)',
                    'icon' => '✅',
                    'route_hint' => 'admin.approvals.*',
                    'access_level' => 'Approve / Reject',
                    'description' => 'Menyetujui (Approve) atau menolak (Reject) pengajuan diskon, refund kasir, koreksi harga, dan penyesuaian stok dari Sales/Kasir/Inventory. Keputusan otomatis mengeksekusi efek ke sistem.',
                ],
                [
                    'name' => 'Audit Trail (Tata Kelola)',
                    'icon' => '📋',
                    'route_hint' => 'admin.audit-logs.*',
                    'access_level' => 'View Only',
                    'description' => 'Melacak seluruh aktivitas perubahan data dan keputusan approval pengguna sistem.',
                ],
            ],
            'allowed_actions' => [
                'Menugaskan teknisi (assign mechanic) ke Work Order yang baru masuk',
                'Menyetujui (Approve) atau menolak (Reject) pengajuan diskon, refund, koreksi harga, dan penyesuaian stok dari staf',
                'Melihat laporan seluruh divisi dan riwayat audit log',
                'Menerima notifikasi pengajuan approval & operasional via ikon 🔔 di topbar',
            ],
            'restricted_actions' => [
                'TIDAK ada menu Showroom (Unit Supercar, Pesanan, Test Drive, Voucher) maupun CRM di sidebar Manager',
                'TIDAK ada menu Booking Servis, AI Sound Scan, POS, Invoice, maupun Gudang di sidebar Manager',
                'Dilarang mengakses mesin kasir (POS) maupun menerima pembayaran langsung dari pelanggan',
                'Dilarang membuat/menghapus Work Order dan menyerahkan unit kendaraan (tugas Kasir)',
                'Dilarang mengubah rincian teknis & status pengerjaan WO (hanya mekanik ter-assign / Super Admin)',
                'Dilarang mengakses modul Manajemen Pengguna dan Konfigurasi Sistem',
                'Dilarang menghapus data permanen dari database sistem',
            ],
        ],

        'sales' => [
            'key' => 'sales',
            'name' => 'Sales Executive',
            'category' => 'Komersial & Penjualan Supercar',
            'badge_class' => 'bg-emerald-400/20 border-emerald-400 text-emerald-400',
            'icon' => '🏎',
            'summary' => 'Fokus pemasaran unit supercar dengan 9 menu sidebar: Dashboard, Unit Supercar, Pesanan Masuk, Jadwal Test Drive, Voucher & Promo (view), Pesan Customer, Tiket Layanan, Ajukan Approval, dan Laporan Penjualan. Tidak ada akses workshop, kasir, gudang, maupun Database Pelanggan.',
            'menus' => [
                [
                    'name' => 'Dashboard (Dashboard Sales)',
                    'icon' => '⬡',
                    'route_hint' => 'admin.dashboard',
                    'access_level' => 'Metrik Penjualan',
                    'description' => 'Statistik revenue/prospek bulan ini, quick-action "+ Tambah Mobil", dan notifikasi test drive/offer.',
                ],
                [
                    'name' => 'Unit Supercar (Showroom & Penjualan)',
                    'icon' => '🏎',
                    'route_hint' => 'admin.cars.* (kecuali destroy)',
                    'access_level' => 'CRUD Tanpa Hapus',
                    'description' => 'Menambah mobil baru ke katalog, memperbarui spesifikasi & harga, unggah foto etalase, inspeksi kondisi fisik, dan set featured.',
                ],
                [
                    'name' => 'Pesanan Masuk (Showroom & Penjualan)',
                    'icon' => '📋',
                    'route_hint' => 'admin.offers.*',
                    'access_level' => 'Terima / Tolak Pesanan',
                    'description' => 'Menerima (Accept) pesanan pembelian untuk men-set unit & menghasilkan Sales Order, atau menolak dengan alasan.',
                ],
                [
                    'name' => 'Jadwal Test Drive (Showroom & Penjualan)',
                    'icon' => '🏁',
                    'route_hint' => 'admin.appointments.*',
                    'access_level' => 'Lihat + Ubah Status',
                    'description' => 'Melihat janji temu dan mengubah status test drive (confirm/completed/missed) dengan calon pembeli.',
                ],
                [
                    'name' => 'Voucher & Promo (Showroom & Penjualan)',
                    'icon' => '🎟️',
                    'route_hint' => 'admin.vouchers.index|show, admin.promotions.index|show',
                    'access_level' => 'View Only',
                    'description' => 'Melihat daftar voucher/promo aktif untuk didampingkan ke customer (penggunaan voucher tetap dilakukan di POS).',
                ],
                [
                    'name' => 'Pesan Customer (CRM & Komunikasi)',
                    'icon' => '📬',
                    'route_hint' => 'admin.contact-messages.*',
                    'access_level' => 'Baca & Balas',
                    'description' => 'Membaca dan membalas pertanyaan konsultasi mobil dari website.',
                ],
                [
                    'name' => 'Tiket Layanan (CRM & Komunikasi)',
                    'icon' => '🎫',
                    'route_hint' => 'admin.tickets.*',
                    'access_level' => 'Baca & Balas',
                    'description' => 'Memproses tiket bantuan/konsultasi dan memperbarui status penanganannya.',
                ],
                [
                    'name' => 'Ajukan Approval (Pengajuan & Persetujuan)',
                    'icon' => '✍️',
                    'route_hint' => 'admin.approvals.create, admin.approvals.store',
                    'access_level' => 'Pengaju (Submit Only)',
                    'description' => 'Mengajukan permohonan diskon transaksi di Pusat Approval untuk disetujui Manager (tidak bisa menyetujui sendiri).',
                ],
                [
                    'name' => 'Laporan Penjualan (Laporan)',
                    'icon' => '📊',
                    'route_hint' => 'admin.reports.index, admin.reports.sales',
                    'access_level' => 'Rekapitulasi Penjualan',
                    'description' => 'Melihat pencapaian target penjualan mobil dan daftar unit yang berhasil terjual.',
                ],
            ],
            'allowed_actions' => [
                'Menginput unit supercar baru ke etalase portal dan mengunggah foto kualitas tinggi',
                'Mengubah detail harga, kilometer, deskripsi, dan catatan inspeksi unit mobil',
                'Menerima (Accept) pesanan masuk untuk konversi menjadi Sales Order resmi',
                'Mengonfirmasi dan memperbarui status janji temu test drive dengan calon pembeli',
                'Membalas pesan konsultasi pelanggan dari website secara langsung',
                'Mengajukan permohonan diskon ke Manager melalui Pusat Approval',
            ],
            'restricted_actions' => [
                'Dilarang menghapus unit mobil dari sistem (Super Admin Only)',
                'Dilarang mengakses Database Pelanggan — URL customers.* menghasilkan 403 untuk Sales',
                'Dilarang mengakses modul workshop: Booking Servis, Work Order, AI Sound Scan',
                'Dilarang mengakses mesin kasir (POS) maupun menerima pembayaran',
                'Dilarang mengakses Gudang: katalog spare part, stok, dan Purchase Order',
                'Dilarang membuat/mengubah voucher dan promo (hanya melihat)',
                'Dilarang menyetujui sendiri pengajuan approval (pembuat pengajuan ≠ penyetuju)',
            ],
        ],

        'cashier' => [
            'key' => 'cashier',
            'name' => 'Kasir / Frontdesk Kasir',
            'category' => 'Keuangan & Frontdesk Operasional',
            'badge_class' => 'bg-purple-400/20 border-purple-400 text-purple-400',
            'icon' => '💳',
            'summary' => 'Frontdesk bengkel & pemegang kas dengan 7 menu sidebar: Dashboard, Booking Servis, Work Order & Antrean, POS Kasir, Invoice & Bukti Bayar, Ajukan Approval, dan Laporan Shift. Tidak menyentuh teknis pengerjaan, showroom, maupun gudang.',
            'menus' => [
                [
                    'name' => 'Dashboard (Dashboard Cashier)',
                    'icon' => '⬡',
                    'route_hint' => 'admin.dashboard',
                    'access_level' => 'Rekapitulasi Shift',
                    'description' => 'Ringkasan omzet bengkel bulan ini, WO aktif, booking hari ini, quick-action POS/WO, dan notifikasi billing siap bayar.',
                ],
                [
                    'name' => 'Booking Servis (Workshop & Servis)',
                    'icon' => '📅',
                    'route_hint' => 'admin.service-bookings.*',
                    'access_level' => 'Frontdesk & Konversi WO',
                    'description' => 'Mendaftarkan customer walk-in, mengonfirmasi booking online, mengubah status, dan mengonversi booking menjadi Work Order.',
                ],
                [
                    'name' => 'Work Order & Antrean (Workshop & Servis)',
                    'icon' => '🔧',
                    'route_hint' => 'admin.work-orders.index|show|create|store|deliver',
                    'access_level' => 'Buat WO, Pantau, & Serah Terima',
                    'description' => 'Membuat WO baru, memantau progress antrean, dan menyerahkan unit (deliver) setelah lunas. Rincian jasa/part & status pengerjaan hanya boleh diubah mekanik ter-assign.',
                ],
                [
                    'name' => 'POS Kasir (Point of Sale)',
                    'icon' => '💳',
                    'route_hint' => 'admin.pos.*',
                    'access_level' => 'Operator Utama POS',
                    'description' => 'Pembayaran servis (WO), pelunasan pembelian mobil, penjualan part ritel; otomatis memberi poin loyalitas, menerapkan voucher, dan cetak struk thermal. Pembayaran mobil/WO otomatis men-deliver unit.',
                ],
                [
                    'name' => 'Invoice & Bukti Bayar (Point of Sale)',
                    'icon' => '🧾',
                    'route_hint' => 'admin.invoices.*',
                    'access_level' => 'Penerbitan & Cetak',
                    'description' => 'Membuka seluruh invoice transaksi, mencetak Struk Thermal 80mm, dan mengunduh PDF.',
                ],
                [
                    'name' => 'Ajukan Approval (Pengajuan & Persetujuan)',
                    'icon' => '✍️',
                    'route_hint' => 'admin.approvals.create, admin.approvals.store',
                    'access_level' => 'Pengaju (Submit Only)',
                    'description' => 'Mengajukan permohonan diskon dan refund pembayaran ke Manager (Pusat Approval), mis. salah input kasir.',
                ],
                [
                    'name' => 'Laporan Shift (Laporan)',
                    'icon' => '📊',
                    'route_hint' => 'admin.reports.index, admin.reports.finance',
                    'access_level' => 'Rekapitulasi Kasir',
                    'description' => 'Melihat laporan penutupan harian (settlement) per metode pembayaran.',
                ],
            ],
            'allowed_actions' => [
                'Mengoperasikan modul Point of Sale (POS) untuk semua transaksi pembayaran',
                'Menerima pembayaran tunai, transfer bank, kartu kredit/debit (EDC), dan QRIS',
                'Mendaftarkan jadwal booking servis baru dan mengonversinya menjadi Work Order aktif',
                'Membuat Work Order baru dan menyerahkan kendaraan (deliver) setelah tagihan lunas',
                'Mencetak struk kasir thermal dan menerbitkan invoice resmi',
                'Mengajukan permohonan diskon dan refund ke Manager melalui Pusat Approval',
            ],
            'restricted_actions' => [
                'Dilarang melakukan void/refund secara mandiri tanpa Approval Manager',
                'Dilarang mengubah status pengerjaan, menambah/menghapus item jasa & part, dan menandai WO selesai (hanya mekanik ter-assign / Super Admin)',
                'Dilarang menugaskan teknisi (assign mechanic) ke Work Order (kewenangan Manager)',
                'Dilarang mengakses modul showroom penjualan mobil (unit, pesanan, test drive, voucher) maupun AI Sound Scan',
                'Dilarang mengakses gudang: katalog spare part, stok, dan Purchase Order',
                'Dilarang menghapus data transaksi, work order, atau invoice dari sistem',
            ],
        ],

        'inventory' => [
            'key' => 'inventory',
            'name' => 'Inventory & Gudang',
            'category' => 'Logistik & Pengelolaan Suku Cadang',
            'badge_class' => 'bg-amber-500/20 border-amber-500 text-amber-500',
            'icon' => '📦',
            'summary' => 'Pengelola tunggal gudang dengan 6 menu sidebar: Dashboard, Katalog Spare Part, Stok & Movement, Purchase Order, Ajukan Approval, dan Laporan Inventori. Tidak ada akses showroom, workshop, kasir, maupun CRM.',
            'menus' => [
                [
                    'name' => 'Dashboard (Dashboard Inventory)',
                    'icon' => '⬡',
                    'route_hint' => 'admin.dashboard',
                    'access_level' => 'Status Gudang Real-Time',
                    'description' => 'Valuasi inventori, jumlah SKU aktif, quick-action "+ Tambah Part"/"Stok & Movement"/PO, dan notifikasi stok menipis.',
                ],
                [
                    'name' => 'Katalog Spare Part (Gudang & Inventori)',
                    'icon' => '⚙️',
                    'route_hint' => 'admin.spare-parts.*',
                    'access_level' => 'CRUD Master Part',
                    'description' => 'Menambah SKU suku cadang baru, mengedit spesifikasi & harga jual, unggah foto part, dan mengatur batas minimum stok.',
                ],
                [
                    'name' => 'Stok & Movement (Gudang & Inventori)',
                    'icon' => '📦',
                    'route_hint' => 'admin.inventory.*',
                    'access_level' => 'Kontrol Stok & Penyesuaian',
                    'description' => 'Melihat kartu riwayat stok (in/out), daftar stok menipis, dan melakukan penyesuaian kecil (adjust stock).',
                ],
                [
                    'name' => 'Purchase Order (Gudang & Inventori)',
                    'icon' => '🛒',
                    'route_hint' => 'admin.purchase-orders.*',
                    'access_level' => 'Buat, Kirim, & Terima PO',
                    'description' => 'Membuat draft PO ke supplier, mengirim pesanan, mencatat barang diterima (sebagian/lunas), dan membatalkan PO. PO hanya untuk pengadaan cabang, bukan jual-beli pribadi.',
                ],
                [
                    'name' => 'Ajukan Approval (Pengajuan & Persetujuan)',
                    'icon' => '✍️',
                    'route_hint' => 'admin.approvals.create, admin.approvals.store',
                    'access_level' => 'Pengaju (Submit Only)',
                    'description' => 'Mengajukan koreksi harga jual part dan penyesuaian stok selisih besar ke Manager (Pusat Approval).',
                ],
                [
                    'name' => 'Laporan Inventori (Laporan)',
                    'icon' => '📊',
                    'route_hint' => 'admin.reports.index, admin.reports.inventory',
                    'access_level' => 'Rekapitulasi Mutasi Barang',
                    'description' => 'Perputaran barang fast/slow moving, nilai total stok gudang, dan riwayat penyesuaian opname.',
                ],
            ],
            'allowed_actions' => [
                'Menambah, mengedit, dan menonaktifkan master data katalog suku cadang (soft delete)',
                'Mengatur batas peringatan stok minimum (low stock threshold) per SKU',
                'Melakukan penyesuaian stok (Stock Adjustment) saat ditemukan selisih fisik opname kecil',
                'Membuat draft Purchase Order (PO), mengirim ke vendor, dan mencatat barang yang diterima',
                'Mengajukan koreksi harga dan penyesuaian stok selisih besar untuk disetujui Manager',
            ],
            'restricted_actions' => [
                'Dilarang menyetujui (Approve) pengajuan buatannya sendiri — pembuat pengajuan ≠ penyetuju (kewenangan Manager)',
                'Dilarang menghapus suku cadang secara permanen (Super Admin Only)',
                'Dilarang mengakses modul showroom mobil, Booking Servis, Work Order, AI Sound Scan, dan kasir POS',
                'Dilarang mengakses CRM: Database Pelanggan, Pesan Customer, Tiket Layanan, dan Loyalitas',
                'Dilarang melihat laporan finansial/laporan divisi lain dan membalas pesan customer',
                'Tidak ada aktivitas jual-beli untuk diri sendiri — PO hanya untuk pengadaan cabang',
            ],
        ],

        'mechanic' => [
            'key' => 'mechanic',
            'name' => 'Mekanik / Teknisi',
            'category' => 'Teknis & Pengerjaan Servis',
            'badge_class' => 'bg-orange-400/20 border-orange-400 text-orange-400',
            'icon' => '🔧',
            'summary' => 'Fokus murni perbaikan fisik dengan 4 menu sidebar: Dashboard, Work Order Saya, AI Sound Scan, dan Katalog Spare Part (lihat stok saja). Perbaikan bengkel saja — tanpa kasir, showroom, gudang CRUD, maupun laporan.',
            'menus' => [
                [
                    'name' => 'Dashboard (Dashboard Mechanic)',
                    'icon' => '⬡',
                    'route_hint' => 'admin.dashboard',
                    'access_level' => 'Antrean Pengerjaan Pribadi',
                    'description' => 'Daftar WO yang ditugaskan ke dirinya, ketersediaan part, data AI scan, dan notifikasi booking/penugasan.',
                ],
                [
                    'name' => 'Work Order Saya (Workshop & Servis)',
                    'icon' => '🔧',
                    'route_hint' => 'admin.work-orders.index|show|status|service-items|part-items|complete',
                    'access_level' => 'Eksekusi Work Order Miliknya',
                    'description' => 'Melihat WO miliknya, memulai servis, menambah/menghapus rincian jasa & part, dan memperbarui status hingga "Completed". Tidak melihat WO mekanik lain.',
                ],
                [
                    'name' => 'AI Sound Scan (Workshop & Servis)',
                    'icon' => '🔊',
                    'route_hint' => 'admin.ai-scans.*',
                    'access_level' => 'Review & Diagnosis',
                    'description' => 'Melihat hasil diagnosis suara mesin, mereview/menindaklanjuti prediksi kerusakan, dan menambahkan catatan.',
                ],
                [
                    'name' => 'Katalog Spare Part (Gudang & Inventori)',
                    'icon' => '⚙️',
                    'route_hint' => 'admin.spare-parts.index, admin.spare-parts.show',
                    'access_level' => 'View Only',
                    'description' => 'Melihat ketersediaan sisa stok suku cadang di rak gudang untuk keperluan pengerjaan servis.',
                ],
            ],
            'allowed_actions' => [
                'Melihat detail kendaraan dan instruksi pekerjaan pada Work Order miliknya (yang ditugaskan ke dirinya)',
                'Memulai pengerjaan servis (Start / In Progress)',
                'Mencatat rincian jasa pengerjaan dan mengambil suku cadang dari katalog untuk dimasukkan ke WO',
                'Mengajukan perbaikan ke tahap Quality Control (QC) setelah pekerjaan fisik selesai',
                'Menandai servis selesai (Completed) agar notifikasi tagihan siap bayar terkirim ke Kasir',
                'Merekam & mereview isi pemindaian suara mesin di modul AI Sound Scan',
            ],
            'restricted_actions' => [
                'Dilarang melihat Work Order milik teknisi/mekanik lain',
                'Dilarang mengubah status menjadi "Delivered" (eksklusif Kasir setelah lunas)',
                'Dilarang mengakses kasir (POS), invoice, maupun menerima uang',
                'Dilarang membuat/mengubah katalog spare part atau menyesuaikan stok (hanya melihat)',
                'Dilarang mengakses showroom mobil (unit, pesanan, test drive, voucher), CRM, dan Laporan',
                'Dilarang menghapus Work Order dari sistem',
                'Role mekanik tidak terdaftar sebagai pengaju/penyetuju di Pusat Approval',
            ],
        ],

        'customer' => [
            'key' => 'customer',
            'name' => 'Customer / Pelanggan',
            'category' => 'Portal Publik & Member',
            'badge_class' => 'bg-gray-400/20 border-gray-400 text-gray-300',
            'icon' => '👤',
            'summary' => 'Pengguna portal publik dan member area: melihat katalog supercar, melakukan booking servis mandiri, memantau riwayat mobil di garasi, dan menerima pengingat perawatan. Seluruh menu berada di portal /customer — bukan dashboard administrator.',
            'menus' => [
                [
                    'name' => 'Portal Pelanggan & Notifikasi',
                    'icon' => '📱',
                    'route_hint' => 'customer.dashboard, customer.notifications.*',
                    'access_level' => 'Data Pribadi Mandiri',
                    'description' => 'Status pengerjaan kendaraan, aktivitas terkini, poin loyalitas, dan kotak notifikasi pribadi (booking, pesanan, pengingat).',
                ],
                [
                    'name' => 'Garasi Virtual',
                    'icon' => '🏎',
                    'route_hint' => 'customer.vehicles.*',
                    'access_level' => 'Kendaraan Milik Sendiri',
                    'description' => 'Mendaftarkan, melihat, dan mengedit data supercar milik pribadi yang terdaftar untuk layanan AUDrive.',
                ],
                [
                    'name' => 'Booking Servis Mandiri',
                    'icon' => '📅',
                    'route_hint' => 'customer.service-bookings.*',
                    'access_level' => 'Pemesanan Jadwal Online',
                    'description' => 'Memilih tanggal, waktu, dan jenis paket servis berkala dari perangkat pribadi, serta memantau statusnya.',
                ],
                [
                    'name' => 'Jadwal & Pengingat Servis',
                    'icon' => '⏰',
                    'route_hint' => 'customer.reminders.index, customer.reminders.dismiss',
                    'access_level' => 'Reminder Personal',
                    'description' => 'Panel pengingat aktif dari sistem (servis berkala/STNK/garansi) yang dapat diabaikan (dismiss).',
                ],
                [
                    'name' => 'Riwayat Transaksi & Kwitansi',
                    'icon' => '🧾',
                    'route_hint' => 'customer.invoices.*',
                    'access_level' => 'Dokumen Digital Pribadi',
                    'description' => 'Melihat riwayat pembayaran dan mengunduh invoice digital resmi AUDrive.',
                ],
                [
                    'name' => 'AI Sound Scan (Kendaraan Sendiri)',
                    'icon' => '🎙️',
                    'route_hint' => 'customer.ai-scan.*',
                    'access_level' => 'Hasil Diagnostik Pribadi',
                    'description' => 'Mengajukan pemeriksaan suara mesin kendaraannya dan melihat hasil analisis AI.',
                ],
                [
                    'name' => 'Tiket Layanan & Bantuan',
                    'icon' => '💬',
                    'route_hint' => 'customer.tickets.*',
                    'access_level' => 'Dukungan Pelanggan',
                    'description' => 'Mengajukan pertanyaan, konsultasi perawatan, atau komplain langsung kepada manajemen.',
                ],
                [
                    'name' => 'Voucher & Poin Loyalitas',
                    'icon' => '🎟️',
                    'route_hint' => 'customer.vouchers.*, customer.loyalty.*',
                    'access_level' => 'Benefit Member',
                    'description' => 'Melihat voucher miliknya, promosi aktif, dan riwayat poin loyalitas.',
                ],
            ],
            'allowed_actions' => [
                'Mengajukan janji temu servis tunggal (service booking) secara online',
                'Melihat status pengerjaan servis mobil di bengkel secara transparan',
                'Membeli mobil di marketplace publik atau mengajukan penawaran langsung',
                'Mengunduh invoice resmi dan kwitansi pembayaran untuk klaim garansi servis',
                'Menerima pengingat sistem (reminder) dan mengabaikannya bila sudah tanggapi',
                'Memberikan ulasan bintang (review) atas performa teknisi dan pelayanan showroom',
            ],
            'restricted_actions' => [
                'Dilarang keras mengakses panel dashboard administrasi internal (/admin/*)',
                'Dilarang melihat data kendaraan, invoice, atau riwayat pelanggan lain',
                'Dilarang mengubah status operasional, harga suku cadang, atau jadwal bengkel',
            ],
        ],
    ];

    /**
     * Mengembalikan seluruh matriks hak akses role.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAllRoles(): array
    {
        return $this->matrix;
    }

    /**
     * Mengembalikan keterangan hak akses untuk satu role tertentu.
     *
     * @param  string  $roleKey  Key role, mis. 'super-admin', 'sales', 'customer'.
     * @return array<string, mixed>|null Null bila role tidak terdaftar di matriks.
     */
    public function getRole(string $roleKey): ?array
    {
        return $this->matrix[$roleKey] ?? null;
    }
}
