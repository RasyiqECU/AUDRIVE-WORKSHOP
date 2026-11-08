# AUDrive QA & Hardening Checklist

> Status faktual per **8 November 2026**. Item ✅ = terverifikasi ada/bekerja di
> repo; item ⬜ = **belum diimplementasikan** (roadmap), tidak bisa ditandai selesai.

## Ringkasan Test Suite (Pest)

- **83 test** • **80 passed** • **3 skipped** • **242 assertions** (hasil `composer test`)
- Database test: **MySQL khusus `audrive_test`** — terpisah dari database aplikasi
  `audrive`, setiap run di-refresh (`RefreshDatabase` → `migrate:fresh`).

### 1. Route Validation ✅
- [x] Route admin dilindungi middleware auth + role
- [x] Route customer dilindungi middleware auth
- [x] Route API menggunakan prefix /api/v1
- [x] Health check endpoint tersedia

### 2. Form Request Validation ✅
- [x] Validasi lengkap per-area (StoreCarUnit, StoreOffer, StoreTestDrive,
      StoreServiceBooking, StoreAiScan, StoreCartItem, Checkout, StoreVehicle, dst.)

### 3. Policy Authorization ✅
- [x] AiScanPolicy, CarUnitPolicy, OfferPolicy, AppointmentPolicy, WorkOrderPolicy,
      SparePartPolicy, SalesOrderPolicy, PurchaseOrderPolicy, dst. terdaftar
      di `AppServiceProvider`

### 4. Eager Loading & N+1 Prevention ✅ (tercover test)

### 5. Input Validation Consistency ✅
- [x] Price integer IDR, tanggal `Y-m-d`, jam `H:i`, backed enum

### 6. CSRF Protection ✅ (tercover test forms)

### 7. Rate Limiting ✅ (login & API — tercover test)

### 8. File Upload Security ✅ (tercover test tipe & ukuran file)

### 9–12. Authorization & Security Hardening ✅ (tercover QAHardeningTest)

### 13. Testing Coverage ✅
- [x] AuthTest, ApiTest, MarketplaceTest, WorkshopTest, SparePart/InvoiceRevamp,
      SupportServicesTest, QAHardeningTest, WorkshopTest

### 14. CI/CD Pipeline ⬜ **belum ada**
- [ ] GitHub Actions workflow
- [ ] Code style check Pint otomatis
- [ ] Static analysis PHPStan (tidak ter-install)
- [ ] Security audit (`composer audit`) otomatis
- [ ] Coverage reporting
- [ ] Staging/production deployment

### 15. Documentation ✅ / ⬜
- [x] README dengan setup instructions (`docs/README.md`)
- [x] Struktur & konteks project (`docs/STRUCTURE.md`, `docs/AUDrive_Context.md`)
- [x] Environment variables kunci didokumentasikan
- [ ] API documentation (.openapi/blueprint — belum ada)
- [ ] Database schema documentation terpusat (tersedia `php artisan db:table`, belum ada doc)
- [ ] Deployment guide

## 🐛 Known Issues (faktual)

- CI/CD, PHPStan, Horizon monitoring: **belum ada** di project.
- `docker/` masih placeholder kosong.
- Test suite memerlukan MySQL XAMPP berjalan (test DB `audrive_test`).

## 📊 Test Coverage

- Hasil terukur: 83 test / 242 assertions (80 pass, 3 skip)
- Target coverage 80% + laporan coverage: **belum disetel** (Xdebug/PCOV + codecov belum ada)

## 🚀 Deployment Checklist

- [ ] Environment variables production tersusun
- [ ] Database migration + seeder production
- [ ] Queue workers berjalan (`php artisan queue:work` / supervisor)
- [ ] `php artisan storage:link` dijalankan
- [ ] SSL & domain terkonfigurasi
- [ ] Monitoring & backup strategy
- [ ] (Opsional masa depan) Horizon + Redis queue

---

Last updated: 2026-11-08 — dikoreksi agar mencerminkan kondisi repo sebenarnya.
