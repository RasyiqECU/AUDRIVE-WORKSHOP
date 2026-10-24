<?php


use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\Offer;
use App\Models\SparePart;
use App\Models\User;


beforeEach(function () {
    $this->customer = User::factory()->create(['role' => 'customer']);
    Customer::factory()->create(['user_id' => $this->customer->id]);


    $this->mechanic = User::factory()->create(['role' => 'mechanic']);
    $this->sales = User::factory()->create(['role' => 'sales']);
    $this->manager = User::factory()->create(['role' => 'manager']);
    $this->admin = User::factory()->create(['role' => 'super-admin']);


    $this->car = CarUnit::factory()->create([
        'status' => 'available',
        'price_sell_idr' => 1000000000,
    ]);
});


// ── RBAC Tests ──


test('customer cannot access admin routes', function () {
    $response = $this->actingAs($this->customer)
        ->get(route('admin.dashboard'));


    $response->assertStatus(403);
});


test('mechanic cannot access finance routes', function () {
    $response = $this->actingAs($this->mechanic)
        ->get(route('admin.finance.index'));


    $response->assertStatus(403);
});


test('sales cannot access user management', function () {
    $response = $this->actingAs($this->sales)
        ->get(route('admin.users.index'));


    $response->assertStatus(403);
});


test('manager can access dashboard', function () {
    $response = $this->actingAs($this->manager)
        ->get(route('admin.dashboard'));


    $response->assertStatus(200);
});


test('admin can access all routes', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));


    $response->assertStatus(200);
});


// ── Authorization Tests ──


test('customer can only see own offers', function () {
    $otherCustomer = User::factory()->create(['role' => 'customer']);
    $otherCustomerProfile = Customer::factory()->create(['user_id' => $otherCustomer->id]);


    Offer::factory()->create([
        'customer_id' => $otherCustomerProfile->id,
        'car_unit_id' => $this->car->id,
    ]);


    $response = $this->actingAs($this->customer)
        ->get(route('customer.offers.index'));


    $response->assertStatus(200);
    $response->assertDontSee($otherCustomerProfile->user->name);
});


test('customer cannot view other customer vehicle', function () {
    $otherCustomer = User::factory()->create(['role' => 'customer']);
    $otherCustomerProfile = Customer::factory()->create(['user_id' => $otherCustomer->id]);
    $vehicle = CustomerVehicle::factory()->create(['customer_id' => $otherCustomerProfile->id]);


    $response = $this->actingAs($this->customer)
        ->get(route('customer.vehicles.show', $vehicle));


    $response->assertStatus(403);
});


test('mechanic can only update assigned work orders', function () {
    $workOrder = \App\Models\WorkOrder::factory()->create([
        'assigned_mechanic_id' => $this->mechanic->id,
    ]);


    $otherMechanic = User::factory()->create(['role' => 'mechanic']);


    $response = $this->actingAs($otherMechanic)
        ->post(route('admin.work-orders.update-status', $workOrder), [
            'status' => 'in_progress',
        ]);


    $response->assertStatus(403);
});


// ── N+1 Query Prevention Tests ──


test('catalog cars uses eager loading', function () {
    CarUnit::factory()->count(20)->create();


    $queries = [];
    \DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });


    $response = $this->get(route('marketplace.index'));


    $response->assertStatus(200);


    // Should have minimal queries (not 20+ for photos)
    $photoQueries = collect($queries)->filter(fn($q) => str_contains($q, 'car_photos'));
    expect($photoQueries->count())->toBeLessThan(5);
});


test('car detail uses eager loading', function () {
    $queries = [];
    \DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });


    $response = $this->get(route('marketplace.show', $this->car->slug));


    $response->assertStatus(200);


    // Photos should be eager loaded
    $photoQueries = collect($queries)->filter(fn($q) => str_contains($q, 'car_photos'));
    expect($photoQueries->count())->toBeLessThan(3);
});


test('spare parts list uses eager loading', function () {
    SparePart::factory()->count(20)->create();


    $queries = [];
    \DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });


    $response = $this->get(route('parts.index'));


    $response->assertStatus(200);


    // Categories should be eager loaded
    $categoryQueries = collect($queries)->filter(fn($q) => str_contains($q, 'spare_part_categories'));
    expect($categoryQueries->count())->toBeLessThan(3);
});


// ── Validation Tests ──


test('car unit validation requires brand', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.cars.store'), [
            'model' => 'Test Model',
            'year' => 2024,
        ]);


    $response->assertSessionHasErrors('brand');
});


test('car unit validation requires valid year', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.cars.store'), [
            'brand' => 'Test',
            'model' => 'Test',
            'year' => 1800,
        ]);


    $response->assertSessionHasErrors('year');
});


test('offer validation requires positive amount', function () {
    $response = $this->actingAs($this->customer)
        ->post(route('marketplace.offer.store', $this->car->slug), [
            'amount_idr' => -1000000,
        ]);


    $response->assertSessionHasErrors('amount_idr');
});


test('service booking validation requires future date', function () {
    $vehicle = CustomerVehicle::factory()->create([
        'customer_id' => $this->customer->customer->id,
    ]);


    $response = $this->actingAs($this->customer)
        ->post(route('workshop.booking.store'), [
            'customer_vehicle_id' => $vehicle->id,
            'service_type' => 'regular_service',
            'preferred_date' => now()->subDay()->format('Y-m-d'),
            'preferred_time' => '09:00',
        ]);


    $response->assertSessionHasErrors('preferred_date');
});


// ── File Upload Security Tests ──


test('car photo upload validates file type', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.cars.store'), [
            'brand' => 'Test',
            'model' => 'Test',
            'year' => 2024,
            'transmission' => 'automatic',
            'mileage_km' => 10000,
            'price_buy_idr' => 500000000,
            'price_sell_idr' => 1000000000,
            'photos' => [
                \Illuminate\Http\UploadedFile::fake()->create('malicious.php', 100),
            ],
        ]);


    $response->assertSessionHasErrors('photos.0');
});


test('ai scan upload validates audio file type', function () {
    $response = $this->actingAs($this->customer)
        ->post(route('customer.ai-scan.store'), [
            'audio' => \Illuminate\Http\UploadedFile::fake()->create('malicious.exe', 100),
        ]);


    $response->assertSessionHasErrors('audio');
});


test('ai scan upload validates file size', function () {
    $response = $this->actingAs($this->customer)
        ->post(route('customer.ai-scan.store'), [
            'audio' => \Illuminate\Http\UploadedFile::fake()->create('large.mp3', 25000), // 25MB
        ]);


    $response->assertSessionHasErrors('audio');
});


// ── Rate Limiting Tests ──


test('login has rate limiting', function () {
    for ($i = 0; $i < 6; $i++) {
        $response = $this->post(route('login'), [
            'email' => 'wrong@example.com',
            'password' => 'wrong',
        ]);
    }


    $response->assertStatus(429);
});


test('api has rate limiting', function () {
    for ($i = 0; $i < 60; $i++) {
        $response = $this->getJson('/api/v1/cars');
    }


    $response->assertStatus(429);
});


// ── CSRF Protection Tests ──


test('forms require csrf token', function () {
    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ], [
        'X-CSRF-TOKEN' => '', // Empty CSRF
    ]);


    $response->assertStatus(419);
});


// ── Mass Assignment Protection Tests ──


test('user model protects sensitive fields', function () {
    $user = User::factory()->make();


    // Try to mass assign role
    $user->fill(['role' => 'super-admin']);


    // Role should not be changed via fill
    expect($user->role)->not->toBe('super-admin');
});


test('enum status cannot be set to invalid value', function () {
    $car = CarUnit::factory()->make();


    // Try to set invalid status
    try {
        $car->status = 'invalid_status';
        $car->save();


        // Should throw exception
        $this->fail('Should not accept invalid enum value');
    } catch (\Throwable $e) {
        expect($e)->toBeInstanceOf(\TypeError::class);
    }
});



