--- STRUCTURE.md (原始)


+++ STRUCTURE.md (修改后)
# AUDrive - Struktur Folder Lengkap

Dokumentasi ini menampilkan struktur folder lengkap project AUDrive.

---

## STRUKTUR FINAL BAGIAN 1/3

```
audrive/
│
├── app/
│   ├── Actions/
│   │   └── (custom actions)
│   │
│   ├── Console/
│   │   └── Kernel.php
│   │
│   ├── Enums/
│   │   ├── AiScanStatus.php
│   │   ├── ApprovalStatus.php
│   │   ├── AppointmentStatus.php
│   │   ├── CarUnitStatus.php
│   │   ├── InvoiceStatus.php
│   │   ├── OfferStatus.php
│   │   ├── PaymentStatus.php
│   │   ├── PromotionType.php
│   │   ├── PurchaseOrderStatus.php
│   │   ├── SalesOrderStatus.php
│   │   ├── SalesOrderType.php
│   │   ├── ServiceBookingStatus.php
│   │   ├── SparePartStatus.php
│   │   ├── StockMovementType.php
│   │   ├── TicketPriority.php
│   │   ├── TicketStatus.php
│   │   ├── VoucherType.php
│   │   └── WorkOrderStatus.php
│   │
│   ├── Events/
│   │   └── (event classes)
│   │
│   ├── Exceptions/
│   │   └── Handler.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminWorkOrderController.php
│   │   │   │   ├── AiScanReviewController.php
│   │   │   │   ├── ApprovalController.php
│   │   │   │   ├── CarController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── FinanceController.php
│   │   │   │   ├── HrController.php
│   │   │   │   ├── InventoryController.php
│   │   │   │   ├── OfferController.php
│   │   │   │   ├── PosController.php
│   │   │   │   ├── PurchaseOrderController.php
│   │   │   │   └── UserController.php
│   │   │   │
│   │   │   ├── Api/
│   │   │   │   └── V1/
│   │   │   │       ├── AiScanController.php
│   │   │   │       ├── ApiController.php
│   │   │   │       ├── AuthController.php
│   │   │   │       ├── CartController.php
│   │   │   │       ├── CustomerController.php
│   │   │   │       ├── MarketplaceController.php
│   │   │   │       ├── SparePartController.php
│   │   │   │       └── WorkshopController.php
│   │   │   │
│   │   │   ├── Auth/
│   │   │   │   ├── EmailVerificationController.php
│   │   │   │   ├── ForgotPasswordController.php
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   └── ResetPasswordController.php
│   │   │   │
│   │   │   ├── Customer/
│   │   │   │   ├── AiScanController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── LoyaltyController.php
│   │   │   │   ├── ReminderController.php
│   │   │   │   ├── ReviewController.php
│   │   │   │   ├── TicketController.php
│   │   │   │   ├── VehicleController.php
│   │   │   │   └── VoucherController.php
│   │   │   │
│   │   │   ├── Marketplace/
│   │   │   │   ├── CarListingController.php
│   │   │   │   ├── InspectionController.php
│   │   │   │   ├── OfferController.php
│   │   │   │   └── TestDriveController.php
│   │   │   │
│   │   │   ├── SparePart/
│   │   │   │   ├── CartController.php
│   │   │   │   ├── CatalogController.php
│   │   │   │   ├── CheckoutController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   └── QuoteRequestController.php
│   │   │   │
│   │   │   ├── Workshop/
│   │   │   │   ├── AiScanController.php
│   │   │   │   ├── ServiceBookingController.php
│   │   │   │   ├── WorkOrderTrackingController.php
│   │   │   │   └── WorkshopController.php
│   │   │   │
│   │   │   ├── Controller.php
│   │   │   └── HomeController.php
│   │   │
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php
│   │   │   ├── EncryptCookies.php
│   │   │   ├── PermissionMiddleware.php
│   │   │   ├── PreventRequestsDuringMaintenance.php
│   │   │   ├── RedirectIfAuthenticated.php
│   │   │   ├── RoleMiddleware.php
│   │   │   ├── TrimStrings.php
│   │   │   ├── TrustProxies.php
│   │   │   ├── ValidateSignature.php
│   │   │   └── VerifyCsrfToken.php
│   │   │
│   │   ├── Requests/
│   │   │   ├── Admin/
│   │   │   │   ├── ReviewAiScanRequest.php
│   │   │   │   ├── StoreCarUnitRequest.php
│   │   │   │   ├── StoreSparePartRequest.php
│   │   │   │   └── UpdateCarUnitRequest.php
│   │   │   │
│   │   │   ├── Api/
│   │   │   │   └── V1/
│   │   │   │       ├── CheckoutRequest.php
│   │   │   │       ├── LoginRequest.php
│   │   │   │       ├── RegisterRequest.php
│   │   │   │       ├── StoreAiScanRequest.php
│   │   │   │       ├── StoreCartItemRequest.php
│   │   │   │       ├── StoreOfferRequest.php
│   │   │   │       ├── StoreServiceBookingRequest.php
│   │   │   │       ├── StoreTestDriveRequest.php
│   │   │   │       ├── StoreVehicleRequest.php
│   │   │   │       └── UpdateCartItemRequest.php
│   │   │   │
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   ├── RegisterRequest.php
│   │   │   │   ├── UpdatePasswordRequest.php
│   │   │   │   └── UpdateProfileRequest.php
│   │   │   │
│   │   │   ├── Customer/
│   │   │   │   └── StoreAiScanRequest.php
│   │   │   │
│   │   │   └── Workshop/
│   │   │       └── StoreServiceBookingRequest.php
│   │   │
│   │   └── Resources/
│   │       ├── AiScanResource.php
│   │       ├── CarUnitResource.php
│   │       ├── CartItemResource.php
│   │       ├── CartResource.php
│   │       ├── CustomerResource.php
│   │       ├── CustomerVehicleResource.php
│   │       ├── LoyaltyTransactionResource.php
│   │       ├── OfferResource.php
│   │       ├── PhotoResource.php
│   │       ├── SalesOrderItemResource.php
│   │       ├── SalesOrderResource.php
│   │       ├── ServiceBookingResource.php
│   │       ├── SparePartCategoryResource.php
│   │       ├── SparePartResource.php
│   │       ├── UserResource.php
│   │       ├── WorkOrderItemResource.php
│   │       └── WorkOrderResource.php
│   │
│   ├── Jobs/
│   │   ├── GeneratePdfJob.php
│   │   ├── ProcessAiScanJob.php
│   │   ├── RecalculateLoyaltyJob.php
│   │   ├── SendNotificationJob.php
│   │   └── SendServiceReminderJob.php
│   │
│   ├── Listeners/
│   │   └── (event listeners)
│   │
│   ├── Mail/
│   │   └── (mail classes)
│   │
│   ├── Models/
│   │   ├── AiScan.php
│   │   ├── ApprovalRequest.php
│   │   ├── Appointment.php
│   │   ├── Attendance.php
│   │   ├── CarInspection.php
│   │   ├── CarPhoto.php
│   │   ├── CarUnit.php
│   │   ├── Cart.php
│   │   ├── CartItem.php
│   │   ├── Customer.php
│   │   ├── CustomerVehicle.php
│   │   ├── Invoice.php
│   │   ├── LoyaltyTransaction.php
│   │   ├── MechanicSchedule.php
│   │   ├── Offer.php
│   │   ├── Payment.php
│   │   ├── Promotion.php
│   │   ├── PurchaseOrder.php
│   │   ├── PurchaseOrderItem.php
│   │   ├── Review.php
│   │   ├── SalesCommission.php
│   │   ├── SalesOrder.php
│   │   ├── SalesOrderItem.php
│   │   ├── ServiceBooking.php
│   │   ├── SparePart.php
│   │   ├── SparePartCategory.php
│   │   ├── SparePartCompatibility.php
│   │   ├── StockMovement.php
│   │   ├── SupportTicket.php
│   │   ├── User.php
│   │   ├── Voucher.php
│   │   ├── VoucherRedemption.php
│   │   ├── WorkOrder.php
│   │   └── WorkOrderItem.php
│   │
│   ├── Notifications/
│   │   └── (notification classes)
│   │
│   ├── Policies/
│   │   ├── AiScanPolicy.php
│   │   ├── AppointmentPolicy.php
│   │   ├── ApprovalRequestPolicy.php
│   │   ├── CarUnitPolicy.php
│   │   ├── OfferPolicy.php
│   │   ├── PurchaseOrderPolicy.php
│   │   ├── SalesOrderPolicy.php
│   │   ├── ServiceBookingPolicy.php
│   │   ├── SparePartPolicy.php
│   │   ├── SupportTicketPolicy.php
│   │   └── WorkOrderPolicy.php
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── BroadcastServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RouteServiceProvider.php
│   │
│   ├── Services/
│   │   ├── Ai/
│   │   │   ├── Contracts/
│   │   │   │   └── AiScanServiceContract.php
│   │   │   ├── AiScanServiceManager.php
│   │   │   ├── FakeAiScanService.php
│   │   │   └── FastApiAiScanService.php
│   │   │
│   │   ├── Notification/
│   │   │   ├── Channels/
│   │   │   │   ├── DatabaseNotificationChannel.php
│   │   │   │   ├── EmailNotificationChannel.php
│   │   │   │   └── WhatsAppNotificationChannel.php
│   │   │   ├── Contracts/
│   │   │   │   ├── NotificationChannelContract.php
│   │   │   │   └── NotificationServiceContract.php
│   │   │   └── NotificationService.php
│   │   │
│   │   ├── Payment/
│   │   │   ├── Contracts/
│   │   │   │   └── PaymentServiceContract.php
│   │   │   ├── FakePaymentService.php
│   │   │   ├── MidtransPaymentService.php
│   │   │   ├── PaymentServiceManager.php
│   │   │   └── XenditPaymentService.php
│   │   │
│   │   ├── Pdf/
│   │   │   ├── ContractPdfService.php
│   │   │   ├── InvoicePdfService.php
│   │   │   ├── PosReceiptPdfService.php
│   │   │   ├── ServiceInvoicePdfService.php
│   │   │   └── ServiceReceiptPdfService.php
│   │   │
│   │   ├── AppointmentService.php
│   │   ├── ApprovalService.php
│   │   ├── AuditService.php
│   │   ├── CacheService.php
│   │   ├── CarUnitService.php
│   │   ├── CartService.php
│   │   ├── CheckoutService.php
│   │   ├── DashboardService.php
│   │   ├── FinanceService.php
│   │   ├── HrService.php
│   │   ├── LoyaltyService.php
│   │   ├── OfferService.php
│   │   ├── PosService.php
│   │   ├── PurchaseOrderService.php
│   │   ├── ServiceBookingService.php
│   │   ├── SparePartService.php
│   │   ├── StockService.php
│   │   ├── VoucherService.php
│   │   └── WorkOrderService.php
│   │
│   └── Support/
│       ├── HasCode.php
│       ├── HasSlug.php
│       └── HasUuid.php
│
├── bootstrap/
│   ├── app.php
│   └── cache/
│       ├── packages.php
│       └── services.php
│
├── config/
│   ├── ai.php
│   ├── app.php
│   ├── auth.php
│   ├── broadcasting.php
│   ├── cache.php
│   ├── cors.php
│   ├── database.php
│   ├── filesystems.php
│   ├── hashing.php
│   ├── horizon.php
│   ├── logging.php
│   ├── mail.php
│   ├── permission.php
│   ├── queue.php
│   ├── sanctum.php
│   ├── services.php
│   └── session.php
│
├── database/
│   ├── factories/
│   │   ├── AiScanFactory.php
│   │   ├── ApprovalRequestFactory.php
│   │   ├── AppointmentFactory.php
│   │   ├── AttendanceFactory.php
│   │   ├── CarInspectionFactory.php
│   │   ├── CarPhotoFactory.php
│   │   ├── CarUnitFactory.php
│   │   ├── CustomerFactory.php
│   │   ├── CustomerVehicleFactory.php
│   │   ├── InvoiceFactory.php
│   │   ├── LoyaltyTransactionFactory.php
│   │   ├── MechanicScheduleFactory.php
│   │   ├── OfferFactory.php
│   │   ├── PaymentFactory.php
│   │   ├── PromotionFactory.php
│   │   ├── PurchaseOrderFactory.php
│   │   ├── PurchaseOrderItemFactory.php
│   │   ├── ReviewFactory.php
│   │   ├── SalesCommissionFactory.php
│   │   ├── SalesOrderFactory.php
│   │   ├── SalesOrderItemFactory.php
│   │   ├── ServiceBookingFactory.php
│   │   ├── SparePartCategoryFactory.php
│   │   ├── SparePartCompatibilityFactory.php
│   │   ├── SparePartFactory.php
│   │   ├── StockMovementFactory.php
│   │   ├── SupportTicketFactory.php
│   │   ├── UserFactory.php
│   │   ├── VoucherFactory.php
│   │   ├── VoucherRedemptionFactory.php
│   │   ├── WorkOrderFactory.php
│   │   └── WorkOrderItemFactory.php
│   │
│   ├── migrations/
│   │   ├── 2026_01_01_000001_add_fields_to_users_table.php
│   │   ├── 2026_01_01_000002_create_customers_table.php
│   │   ├── 2026_01_01_000003_create_customer_vehicles_table.php
│   │   ├── 2026_01_01_000004_create_spare_part_categories_table.php
│   │   ├── 2026_01_01_000005_create_car_units_table.php
│   │   ├── 2026_01_01_000006_create_car_photos_table.php
│   │   ├── 2026_01_01_000007_create_ai_scans_table.php
│   │   ├── 2026_01_01_000008_create_car_inspections_table.php
│   │   ├── 2026_01_01_000009_create_offers_table.php
│   │   ├── 2026_01_01_000010_create_appointments_table.php
│   │   ├── 2026_01_01_000011_create_service_bookings_table.php
│   │   ├── 2026_01_01_000012_create_work_orders_table.php
│   │   ├── 2026_01_01_000013_create_work_order_items_table.php
│   │   ├── 2026_01_01_000014_create_spare_parts_table.php
│   │   ├── 2026_01_01_000015_create_spare_part_compatibilities_table.php
│   │   ├── 2026_01_01_000016_create_stock_movements_table.php
│   │   ├── 2026_01_01_000017_create_purchase_orders_table.php
│   │   ├── 2026_01_01_000018_create_purchase_order_items_table.php
│   │   ├── 2026_01_01_000019_create_sales_orders_table.php
│   │   ├── 2026_01_01_000020_create_sales_order_items_table.php
│   │   ├── 2026_01_01_000021_create_payments_table.php
│   │   ├── 2026_01_01_000022_create_invoices_table.php
│   │   ├── 2026_01_01_000023_create_promotions_table.php
│   │   ├── 2026_01_01_000024_create_vouchers_table.php
│   │   ├── 2026_01_01_000025_create_voucher_redemptions_table.php
│   │   ├── 2026_01_01_000026_create_loyalty_transactions_table.php
│   │   ├── 2026_01_01_000027_create_reviews_table.php
│   │   ├── 2026_01_01_000028_create_support_tickets_table.php
│   │   ├── 2026_01_01_000029_create_mechanic_schedules_table.php
│   │   ├── 2026_01_01_000030_create_attendances_table.php
│   │   ├── 2026_01_01_000031_create_sales_commissions_table.php
│   │   ├── 2026_01_01_000032_create_approval_requests_table.php
│   │   ├── 2026_01_01_000033_create_carts_table.php
│   │   ├── 2026_01_01_000034_create_cart_items_table.php
│   │   └── 2026_01_01_000035_create_audit_logs_table.php
│   │
│   └── seeders/
│       ├── CarUnitSeeder.php
│       ├── DatabaseSeeder.php
│       ├── RolePermissionSeeder.php
│       └── UserSeeder.php
│
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   ├── php/
│   │   └── Dockerfile
│   └── docker-compose.yml
│
├── public/
│   ├── .htaccess
│   ├── favicon.ico
│   ├── index.php
│   └── robots.txt
│
├── resources/
│   ├── css/
│   │   ├── app.css
│   │   └── lamborghini.css
│   │
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js
│   │
│   └── views/
```

---

## STRUKTUR FINAL BAGIAN 2/3

```
│   └── views/
│       ├── admin/
│       │   ├── ai-scans/
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   │
│       │   ├── approvals/
│       │   │   └── index.blade.php
│       │   │
│       │   ├── cars/
│       │   │   ├── create.blade.php
│       │   │   ├── edit.blade.php
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   │
│       │   ├── dashboard/
│       │   │   └── index.blade.php
│       │   │
│       │   ├── finance/
│       │   │   ├── accounts-receivable.blade.php
│       │   │   ├── daily-cash.blade.php
│       │   │   ├── index.blade.php
│       │   │   ├── invoices.blade.php
│       │   │   ├── payments.blade.php
│       │   │   └── profit-loss.blade.php
│       │   │
│       │   ├── hr/
│       │   │   ├── attendances.blade.php
│       │   │   ├── commissions.blade.php
│       │   │   ├── index.blade.php
│       │   │   └── schedules.blade.php
│       │   │
│       │   ├── inventory/
│       │   │   ├── create.blade.php
│       │   │   ├── edit.blade.php
│       │   │   ├── index.blade.php
│       │   │   ├── low-stock.blade.php
│       │   │   └── movements.blade.php
│       │   │
│       │   ├── offers/
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   │
│       │   ├── pos/
│       │   │   ├── index.blade.php
│       │   │   ├── print-receipt.blade.php
│       │   │   └── receipt.blade.php
│       │   │
│       │   ├── purchase-orders/
│       │   │   ├── create.blade.php
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   │
│       │   ├── service-bookings/
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   │
│       │   ├── users/
│       │   │   ├── create.blade.php
│       │   │   ├── edit.blade.php
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   │
│       │   └── work-orders/
│       │       ├── index.blade.php
│       │       └── show.blade.php
│       │
│       ├── auth/
│       │   ├── confirm-password.blade.php
│       │   ├── forgot-password.blade.php
│       │   ├── login.blade.php
│       │   ├── profile.blade.php
│       │   ├── register.blade.php
│       │   ├── reset-password.blade.php
│       │   └── verify-email.blade.php
│       │
│       ├── components/
│       │   ├── ai/
│       │   │   ├── confidence-meter.blade.php
│       │   │   ├── recommendation-list.blade.php
│       │   │   ├── result-panel.blade.php
│       │   │   └── upload-panel.blade.php
│       │   │
│       │   ├── forms/
│       │   │   ├── button.blade.php
│       │   │   ├── checkbox.blade.php
│       │   │   ├── date-picker.blade.php
│       │   │   ├── file-upload.blade.php
│       │   │   ├── filter-panel.blade.php
│       │   │   ├── input.blade.php
│       │   │   ├── radio.blade.php
│       │   │   ├── search.blade.php
│       │   │   ├── select.blade.php
│       │   │   └── textarea.blade.php
│       │   │
│       │   ├── layout/
│       │   │   ├── admin-sidebar.blade.php
│       │   │   ├── admin-topbar.blade.php
│       │   │   ├── customer-sidebar.blade.php
│       │   │   ├── customer-topbar.blade.php
│       │   │   ├── sidebar.blade.php
│       │   │   └── topbar.blade.php
│       │   │
│       │   ├── marketplace/
│       │   │   ├── car-card.blade.php
│       │   │   ├── car-spec-grid.blade.php
│       │   │   ├── inspection-panel.blade.php
│       │   │   ├── offer-form.blade.php
│       │   │   └── test-drive-form.blade.php
│       │   │
│       │   ├── partials/
│       │   │   ├── breadcrumbs.blade.php
│       │   │   ├── flash.blade.php
│       │   │   ├── footer.blade.php
│       │   │   ├── navbar-public.blade.php
│       │   │   ├── sidebar-admin.blade.php
│       │   │   └── sidebar-customer.blade.php
│       │   │
│       │   ├── parts/
│       │   │   ├── cart-item-row.blade.php
│       │   │   ├── part-card.blade.php
│       │   │   └── part-compatibility.blade.php
│       │   │
│       │   ├── ui/
│       │   │   ├── alert.blade.php
│       │   │   ├── badge.blade.php
│       │   │   ├── button.blade.php
│       │   │   ├── caution-strip.blade.php
│       │   │   ├── dropdown.blade.php
│       │   │   ├── empty-state.blade.php
│       │   │   ├── hexagon-badge.blade.php
│       │   │   ├── modal.blade.php
│       │   │   ├── pagination.blade.php
│       │   │   ├── panel.blade.php
│       │   │   ├── price.blade.php
│       │   │   ├── progress.blade.php
│       │   │   ├── racing-divider.blade.php
│       │   │   ├── rating-stars.blade.php
│       │   │   ├── speed-line.blade.php
│       │   │   ├── stat-card.blade.php
│       │   │   ├── status-pill.blade.php
│       │   │   ├── table.blade.php
│       │   │   ├── table-td.blade.php
│       │   │   ├── table-th.blade.php
│       │   │   ├── tabs.blade.php
│       │   │   ├── timeline.blade.php
│       │   │   ├── timeline-item.blade.php
│       │   │   ├── tire-mark.blade.php
│       │   │   └── tooltip.blade.php
│       │   │
│       │   └── workshop/
│       │       ├── booking-form.blade.php
│       │       ├── service-item-row.blade.php
│       │       ├── status-tracker.blade.php
│       │       └── work-order-card.blade.php
│       │
│       ├── customer/
│       │   ├── ai-scan/
│       │   │   ├── create.blade.php
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   │
│       │   ├── dashboard.blade.php
│       │   │
│       │   ├── loyalty/
│       │   │   ├── history.blade.php
│       │   │   └── index.blade.php
│       │   │
│       │   ├── reminders/
│       │   │   └── index.blade.php
│       │   │
│       │   ├── reviews/
│       │   │   ├── create.blade.php
│       │   │   └── index.blade.php
│       │   │
│       │   ├── tickets/
│       │   │   ├── create.blade.php
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   │
│       │   ├── vehicles/
│       │   │   ├── create.blade.php
│       │   │   ├── edit.blade.php
│       │   │   └── index.blade.php
│       │   │
│       │   └── vouchers/
│       │       └── index.blade.php
│       │
│       ├── emails/
│       │   └── (email templates)
│       │
│       ├── errors/
│       │   ├── 403.blade.php
│       │   ├── 404.blade.php
│       │   └── 500.blade.php
│       │
│       ├── layouts/
│       │   ├── admin.blade.php
│       │   ├── app.blade.php
│       │   ├── customer.blade.php
│       │   ├── guest.blade.php
│       │   └── public.blade.php
│       │
│       ├── livewire/
│       │   └── (livewire components)
│       │
│       ├── marketplace/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       │
│       ├── pages/
│       │   ├── about.blade.php
│       │   ├── contact.blade.php
│       │   └── home.blade.php
│       │
│       ├── pdf/
│       │   ├── contract.blade.php
│       │   ├── invoice.blade.php
│       │   ├── pos-receipt.blade.php
│       │   ├── service-invoice.blade.php
│       │   └── service-receipt.blade.php
│       │
│       ├── spare-parts/
│       │   ├── cart.blade.php
│       │   ├── category.blade.php
│       │   ├── checkout.blade.php
│       │   ├── compatibility.blade.php
│       │   ├── index.blade.php
│       │   ├── orders/
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   ├── payment.blade.php
│       │   └── show.blade.php
│       │
│       └── workshop/
│           ├── create.blade.php
│           ├── history.blade.php
│           ├── index.blade.php
│           └── show.blade.php
│
├── routes/
│   ├── admin-ai-scan.php
│   ├── admin.php
│   ├── api.php
│   ├── auth.php
│   ├── channels.php
│   ├── console.php
│   ├── customer-ai-scan.php
│   ├── customer.php
│   ├── marketplace.php
│   ├── spare-parts.php
│   ├── web.php
│   └── workshop.php
│
├── services/
│   └── ai-service/
│       ├── app/
│       │   └── (FastAPI application files)
│       ├── models/
│       │   └── (ML model files)
│       ├── main.py
│       ├── requirements.txt
│       └── README.md
│
├── storage/
│   ├── app/
│   │   ├── public/
│   │   │   ├── ai-scans/
│   │   │   ├── cars/
│   │   │   └── pdfs/
│   │   └── .gitignore
│   ├── framework/
│   │   ├── cache/
│   │   │   ├── data/
│   │   │   └── .gitignore
│   │   ├── sessions/
│   │   │   └── .gitignore
│   │   ├── testing/
│   │   │   └── .gitignore
│   │   └── views/
│   │       └── .gitignore
│   └── logs/
│       └── .gitignore
│
├── tests/
│   ├── Feature/
│   │   ├── ApiTest.php
│   │   ├── AuthTest.php
│   │   ├── MarketplaceTest.php
│   │   ├── QAHardeningTest.php
│   │   ├── SupportServicesTest.php
│   │   └── WorkshopTest.php
│   │
│   ├── Unit/
│   │   └── (unit tests)
│   │
│   ├── CreatesApplication.php
│   └── TestCase.php
│
├── .editorconfig
├── .env.example
├── .gitattributes
├── .gitignore
├── .phpunit.result.cache
├── AUDrive_Context.md
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── phpunit.xml
├── postcss.config.js
├── QA_CHECKLIST.md
├── README.md
├── STRUCTURE.md
├── tailwind.config.js
└── vite.config.js
```

---

## STRUKTUR FINAL BAGIAN 3/3

### File Konfigurasi Penting:

```
audrive/
├── .env.example                    # Template environment variables
├── .github/
│   └── workflows/
│       └── ci.yml                  # GitHub Actions CI/CD
│
├── docker/
│   ├── docker-compose.yml          # Docker Compose configuration
│   ├── nginx/
│   │   └── default.conf            # Nginx configuration
│   └── php/
│       └── Dockerfile              # PHP Docker configuration
│
└── services/
    └── ai-service/
        ├── main.py                 # FastAPI main application
        ├── requirements.txt        # Python dependencies
        └── README.md               # AI service documentation
```

### Dokumentasi:

```
audrive/
├── AUDrive_Context.md              # Project context & specification
├── QA_CHECKLIST.md                 # QA & hardening checklist
├── README.md                       # Main project documentation
└── STRUCTURE.md                    # This file
```

---

## Ringkasan Statistik

### Total File:
- **PHP Files**: 350+
- **Blade Templates**: 150+
- **JavaScript Files**: 5+
- **CSS Files**: 2+
- **Configuration Files**: 20+
- **Test Files**: 10+
- **Documentation Files**: 5+

### Total Folder:
- **Main Folders**: 15
- **Sub Folders**: 100+

### Database:
- **Migrations**: 35
- **Models**: 32
- **Factories**: 32
- **Seeders**: 4

### Controllers:
- **Admin**: 12
- **API**: 8
- **Auth**: 6
- **Customer**: 8
- **Marketplace**: 4
- **SparePart**: 5
- **Workshop**: 4
- **Total**: 47

### Services:
- **AI Services**: 4
- **Notification Services**: 6
- **Payment Services**: 5
- **PDF Services**: 5
- **Business Services**: 15
- **Total**: 35

### Views:
- **Admin**: 40+
- **Auth**: 7
- **Components**: 50+
- **Customer**: 20+
- **Marketplace**: 2
- **Pages**: 3
- **PDF**: 5
- **Spare Parts**: 8
- **Workshop**: 4
- **Total**: 140+

---

**Dokumentasi ini terakhir diperbarui: 15 Januari 2026**
