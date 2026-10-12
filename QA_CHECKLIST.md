# AUDrive QA & Hardening Checklist


## 📋 Status: IN PROGRESS


### 1. Route Validation ✅
- [x] Semua route memiliki nama yang konsisten
- [x] Route admin dilindungi middleware auth + role
- [x] Route customer dilindungi middleware auth
- [x] Route API menggunakan prefix /api/v1
- [x] Tidak ada route yang expose data sensitif
- [x] Health check endpoint tersedia


### 2. Form Request Validation ✅
- [x] StoreCarUnitRequest - validasi lengkap
- [x] UpdateCarUnitRequest - validasi lengkap
- [x] StoreOfferRequest - validasi amount positif
- [x] StoreTestDriveRequest - validasi tanggal masa depan
- [x] StoreServiceBookingRequest - validasi lengkap
- [x] StoreAiScanRequest - validasi file audio
- [x] StoreCartItemRequest - validasi qty positif
- [x] CheckoutRequest - validasi shipping method
- [x] StoreVehicleRequest - validasi lengkap


### 3. Policy Authorization ✅
- [x] CarUnitPolicy - CRUD authorization
- [x] OfferPolicy - customer hanya bisa edit offer sendiri
- [x] AppointmentPolicy - customer hanya bisa edit appointment sendiri
- [x] ServiceBookingPolicy - customer hanya bisa edit booking sendiri
- [x] WorkOrderPolicy - mechanic hanya bisa update work order yang di-assign
- [x] AiScanPolicy - customer hanya bisa lihat scan sendiri
- [x] SparePartPolicy - inventory bisa manage stock
- [x] SalesOrderPolicy - customer hanya bisa lihat order sendiri
- [x] PurchaseOrderPolicy - inventory bisa manage PO
- [x] ApprovalRequestPolicy - manager bisa approve/reject
- [x] SupportTicketPolicy - customer hanya bisa lihat ticket sendiri


### 4. Eager Loading & N+1 Prevention ✅
- [x] CarUnit::index - eager load primaryPhoto
- [x] CarUnit::show - eager load photos, inspections
- [x] SparePart::index - eager load category
- [x] WorkOrder::index - eager load vehicle, mechanic
- [x] SalesOrder::index - eager load customer, items
- [x] AiScan::index - eager load vehicle
- [x] ServiceBooking::index - eager load vehicle


### 5. Input Validation Consistency ✅
- [x] Semua price menggunakan integer (IDR)
- [x] Semua date menggunakan format Y-m-d
- [x] Semua time menggunakan format H:i
- [x] Semua enum menggunakan backed enum
- [x] Semua required field memiliki pesan error bahasa Indonesia


### 6. CSRF Protection ✅
- [x] Semua form web menggunakan @csrf
- [x] AJAX request menggunakan X-CSRF-TOKEN header
- [x] API menggunakan Sanctum token authentication
- [x] VerifyCsrfToken middleware aktif


### 7. Rate Limiting ✅
- [x] Login: 5 attempts per minute
- [x] Register: 3 attempts per minute
- [x] API: 60 requests per minute
- [x] AI scan upload: 10 requests per minute
- [x] Password reset: 3 attempts per minute


### 8. File Upload Security ✅
- [x] Image upload: hanya jpg, jpeg, png, webp
- [x] Audio upload: hanya wav, mp3, m4a
- [x] Max file size: 5MB untuk image, 20MB untuk audio
- [x] File disimpan di storage/app/public
- [x] File path tidak expose ke public langsung
- [x] Validasi MIME type di server side


### 9. Authorization Customer ✅
- [x] Customer hanya bisa akses data sendiri
- [x] Customer tidak bisa akses data customer lain
- [x] Customer tidak bisa akses admin routes
- [x] Customer tidak bisa akses mechanic routes
- [x] Customer tidak bisa akses sales routes


### 10. Role Internal Authorization ✅
- [x] Customer: hanya akses portal customer
- [x] Mechanic: hanya akses work order & AI scan
- [x] Sales: hanya akses marketplace & leads
- [x] Cashier: hanya akses POS & booking approval
- [x] Inventory: hanya akses spare part & stock
- [x] Manager: akses dashboard, reports, approval
- [x] Super-admin: akses semua


### 11. Performance Optimization ✅
- [x] Database index pada kolom yang sering di-query
- [x] Pagination pada semua list (max 20 per page)
- [x] Cache untuk data yang jarang berubah:
  - [x] Homepage featured cars (1 hour)
  - [x] Spare part categories (24 hours)
  - [x] Car brands (24 hours)
- [x] Tidak cache data personal
- [x] Query optimization dengan EXPLAIN


### 12. Security Hardening ✅
- [x] Mass assignment protection ($fillable)
- [x] Enum validation (tidak bisa set invalid value)
- [x] File upload path aman (storage/app/public)
- [x] Debug mode disabled di production
- [x] Error pages themed (403, 404, 500)
- [x] SQL injection prevention (Eloquent ORM)
- [x] XSS prevention (Blade escaping)
- [x] Password hashing (bcrypt)
- [x] HTTPS enforced di production


### 13. Testing Coverage ✅
- [x] Auth tests (register, login, logout)
- [x] RBAC tests (role-based access)
- [x] Marketplace tests (catalog, detail, offer, test drive)
- [x] Workshop tests (booking, work order, part usage)
- [x] Spare part tests (cart, checkout, stock)
- [x] AI scan tests (upload, fake result, fallback)
- [x] Payment tests (fake payment success)
- [x] Voucher tests (validation, redemption)
- [x] N+1 query prevention tests
- [x] File upload security tests
- [x] Rate limiting tests
- [x] CSRF protection tests


### 14. CI/CD Pipeline ✅
- [x] GitHub Actions workflow
- [x] Automated testing on push
- [x] Code style checking (Pint)
- [x] Static analysis (PHPStan)
- [x] Security audit (composer audit)
- [x] Coverage reporting (Codecov)
- [x] Staging deployment
- [x] Production deployment


### 15. Documentation ✅
- [x] README.md dengan setup instructions
- [x] API documentation
- [x] Database schema documentation
- [x] Environment variables documentation
- [x] Deployment guide


## 🐛 Known Issues
- None


## 📊 Test Coverage
- Feature Tests: 150+
- Unit Tests: 50+
- Coverage Target: 80%


## 🚀 Deployment Checklist
- [ ] Environment variables configured
- [ ] Database migrated
- [ ] Seeders executed
- [ ] Cache cleared
- [ ] Queue workers running
- [ ] Horizon monitoring active
- [ ] SSL certificate installed
- [ ] DNS configured
- [ ] Monitoring tools active
- [ ] Backup strategy in place


## 📝 Notes
- Semua test harus pass sebelum deploy
- Coverage minimal 80%
- Security audit harus clean
- Performance test harus pass
- Load test harus pass (minimal 1000 concurrent users)


---
Last Updated: 2026-01-15
Status: READY FOR STAGING



