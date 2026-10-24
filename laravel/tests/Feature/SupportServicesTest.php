<?php


use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\User;
use App\Models\Voucher;
use App\Services\LoyaltyService;
use App\Services\Notification\NotificationService;
use App\Services\Payment\FakePaymentService;
use App\Services\Pdf\InvoicePdfService;
use App\Services\VoucherService;


beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'customer']);
    $this->customer = Customer::factory()->create(['user_id' => $this->user->id]);


    $this->order = SalesOrder::factory()->create([
        'customer_id' => $this->customer->id,
        'total_amount' => 1000000,
        'status' => 'pending',
    ]);
});


// ── Payment Tests ──


test('fake payment service can create payment intent', function () {
    $paymentService = new FakePaymentService();


    $result = $paymentService->createPaymentIntent($this->order);


    expect($result['success'])->toBeTrue()
        ->and($result['transaction_id'])->toBeString()
        ->and($result['payment_url'])->toBeString()
        ->and($result['amount'])->toBe($this->order->total_amount);
});


test('fake payment service can simulate success', function () {
    $paymentService = new FakePaymentService();


    $result = $paymentService->createPaymentIntent($this->order);
    $success = $paymentService->simulateSuccess($result['transaction_id']);


    expect($success)->toBeTrue();
});


test('fake payment service can simulate failure', function () {
    $paymentService = new FakePaymentService();


    $result = $paymentService->createPaymentIntent($this->order);
    $success = $paymentService->simulateFailure($result['transaction_id']);


    expect($success)->toBeFalse();
});


// ── Voucher Tests ──


test('valid voucher can be redeemed', function () {
    $voucher = Voucher::factory()->create([
        'code' => 'TEST10',
        'type' => 'percentage',
        'discount_value' => 10,
        'min_purchase' => 500000,
        'max_usage' => 100,
        'used_count' => 0,
        'is_active' => true,
        'expires_at' => now()->addDays(7),
    ]);


    $voucherService = new VoucherService();
    $validation = $voucherService->validate($voucher, $this->customer, $this->order->total_amount);


    expect($validation['valid'])->toBeTrue()
        ->and($validation['errors'])->toBeEmpty();
});


test('expired voucher cannot be redeemed', function () {
    $voucher = Voucher::factory()->create([
        'code' => 'EXPIRED',
        'type' => 'percentage',
        'discount_value' => 10,
        'min_purchase' => 500000,
        'max_usage' => 100,
        'used_count' => 0,
        'is_active' => true,
        'expires_at' => now()->subDays(1),
    ]);


    $voucherService = new VoucherService();
    $validation = $voucherService->validate($voucher, $this->customer, $this->order->total_amount);


    expect($validation['valid'])->toBeFalse()
        ->and($validation['errors'])->toContain('Voucher has expired');
});


test('voucher with minimum purchase not met cannot be redeemed', function () {
    $voucher = Voucher::factory()->create([
        'code' => 'MINPURCHASE',
        'type' => 'percentage',
        'discount_value' => 10,
        'min_purchase' => 2000000,
        'max_usage' => 100,
        'used_count' => 0,
        'is_active' => true,
        'expires_at' => now()->addDays(7),
    ]);


    $voucherService = new VoucherService();
    $validation = $voucherService->validate($voucher, $this->customer, $this->order->total_amount);


    expect($validation['valid'])->toBeFalse()
        ->and($validation['errors'][0])->toContain('Minimum purchase not met');
});


test('inactive voucher cannot be redeemed', function () {
    $voucher = Voucher::factory()->create([
        'code' => 'INACTIVE',
        'type' => 'percentage',
        'discount_value' => 10,
        'min_purchase' => 500000,
        'max_usage' => 100,
        'used_count' => 0,
        'is_active' => false,
        'expires_at' => now()->addDays(7),
    ]);


    $voucherService = new VoucherService();
    $validation = $voucherService->validate($voucher, $this->customer, $this->order->total_amount);


    expect($validation['valid'])->toBeFalse()
        ->and($validation['errors'])->toContain('Voucher is not active');
});


// ── Loyalty Tests ──


test('loyalty points can be added to customer', function () {
    $loyaltyService = new LoyaltyService();


    $initialPoints = $this->customer->loyalty_points;
    $pointsToAdd = 100;


    $transaction = $loyaltyService->addPoints($this->customer, $pointsToAdd, 'Test points');


    expect($transaction)->toBeInstanceOf(\App\Models\LoyaltyTransaction::class)
        ->and($transaction->points)->toBe($pointsToAdd)
        ->and($this->customer->fresh()->loyalty_points)->toBe($initialPoints + $pointsToAdd);
});


test('loyalty points can be redeemed', function () {
    $this->customer->update(['loyalty_points' => 1000]);


    $loyaltyService = new LoyaltyService();


    $pointsToRedeem = 500;
    $transaction = $loyaltyService->redeemPoints($this->customer, $pointsToRedeem, 'Test redemption');


    expect($transaction)->toBeInstanceOf(\App\Models\LoyaltyTransaction::class)
        ->and($transaction->points)->toBe(-$pointsToRedeem)
        ->and($this->customer->fresh()->loyalty_points)->toBe(500);
});


test('cannot redeem more points than available', function () {
    $this->customer->update(['loyalty_points' => 100]);


    $loyaltyService = new LoyaltyService();


    expect(fn() => $loyaltyService->redeemPoints($this->customer, 500, 'Test'))
        ->toThrow(\Exception::class, 'Insufficient loyalty points');
});


// ── Notification Tests ──


test('database notification can be created', function () {
    $notificationService = new NotificationService();


    $result = $notificationService->sendVia('database', $this->user, 'booking_created', [
        'date' => '2026-01-20',
        'time' => '10:00',
    ]);


    expect($result)->toBeTrue()
        ->and($this->user->notifications)->toHaveCount(1);
});


test('notification contains correct data', function () {
    $notificationService = new NotificationService();


    $notificationService->sendVia('database', $this->user, 'payment_success', [
        'amount' => 1000000,
    ]);


    $notification = $this->user->notifications->first();


    expect($notification->data['type'])->toBe('payment_success')
        ->and($notification->data['data']['amount'])->toBe(1000000);
});


// ── PDF Tests ──


test('invoice PDF can be generated without error', function () {
    $pdfService = new InvoicePdfService();


    $pdf = $pdfService->generate($this->order);


    expect($pdf)->toBeInstanceOf(\Barryvdh\DomPDF\PDF::class);
});


test('service invoice PDF can be generated without error', function () {
    $pdfService = new \App\Services\Pdf\ServiceInvoicePdfService();


    $pdf = $pdfService->generate($this->order);


    expect($pdf)->toBeInstanceOf(\Barryvdh\DomPDF\PDF::class);
});



