<?php


use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\SparePart;
use App\Models\User;
use Laravel\Sanctum\Sanctum;


beforeEach(function () {
    // Create test user
    $this->user = User::factory()->create([
        'role' => 'customer',
        'is_active' => true,
    ]);


    // Create customer profile
    $this->customer = Customer::factory()->create([
        'user_id' => $this->user->id,
    ]);


    // Create test car
    $this->car = CarUnit::factory()->create([
        'status' => 'available',
        'price_sell_idr' => 1000000000,
    ]);


    // Create test spare part
    $this->sparePart = SparePart::factory()->create([
        'status' => 'active',
        'stock' => 10,
        'price_idr' => 500000,
    ]);
});


// ── Auth Tests ──


test('user can register', function () {
    $response = $this->postJson('/api/v1/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '081234567890',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);


    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user',
                'token',
            ],
        ]);
});


test('user can login', function () {
    $response = $this->postJson('/api/v1/login', [
        'email' => $this->user->email,
        'password' => 'password',
    ]);


    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user',
                'token',
            ],
        ]);
});


test('user can logout', function () {
    Sanctum::actingAs($this->user);


    $response = $this->postJson('/api/v1/logout');


    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Logout successful',
        ]);
});


test('user can get profile', function () {
    Sanctum::actingAs($this->user);


    $response = $this->getJson('/api/v1/me');


    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user',
                'customer',
            ],
        ]);
});


// ── Marketplace Tests ──


test('guest can get cars list', function () {
    $response = $this->getJson('/api/v1/cars');


    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
            'meta',
        ]);
});


test('guest can get car detail', function () {
    $response = $this->getJson("/api/v1/cars/{$this->car->id}");


    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'brand',
                'model',
                'price_sell_idr',
            ],
        ]);
});


test('customer can create offer', function () {
    Sanctum::actingAs($this->user);


    $response = $this->postJson('/api/v1/offers', [
        'car_unit_id' => $this->car->id,
        'amount_idr' => 900000000,
        'note' => 'Saya tertarik dengan unit ini',
    ]);


    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'car_unit_id',
                'amount_idr',
                'status',
            ],
        ]);
});


test('customer can get offers list', function () {
    Sanctum::actingAs($this->user);


    $response = $this->getJson('/api/v1/offers');


    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
            'meta',
        ]);
});


// ── Workshop Tests ──


test('customer can create service booking', function () {
    Sanctum::actingAs($this->user);


    $vehicle = CustomerVehicle::factory()->create([
        'customer_id' => $this->customer->id,
    ]);


    $response = $this->postJson('/api/v1/service-bookings', [
        'customer_vehicle_id' => $vehicle->id,
        'service_type' => 'regular_service',
        'preferred_date' => now()->addDays(3)->format('Y-m-d'),
        'preferred_time' => '09:00',
        'notes' => 'Servis rutin',
    ]);


    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'service_type',
                'status',
            ],
        ]);
});


test('customer can get service bookings list', function () {
    Sanctum::actingAs($this->user);


    $response = $this->getJson('/api/v1/service-bookings');


    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
            'meta',
        ]);
});


// ── AI Scan Tests ──


test('customer can create AI scan', function () {
    Sanctum::actingAs($this->user);


    $response = $this->postJson('/api/v1/ai-scans', [
        'audio' => \Illuminate\Http\UploadedFile::fake()->create('engine-sound.mp3', 1024),
    ]);


    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'status',
                'audio_path',
            ],
        ]);
});


test('customer can get AI scans list', function () {
    Sanctum::actingAs($this->user);


    $response = $this->getJson('/api/v1/ai-scans');


    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
            'meta',
        ]);
});


// ── Spare Parts Tests ──


test('guest can get spare parts list', function () {
    $response = $this->getJson('/api/v1/spare-parts');


    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
            'meta',
        ]);
});


test('guest can get spare part detail', function () {
    $response = $this->getJson("/api/v1/spare-parts/{$this->sparePart->id}");


    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'price_idr',
            ],
        ]);
});


// ── Cart & Checkout Tests ──


test('customer can add item to cart', function () {
    Sanctum::actingAs($this->user);


    $response = $this->postJson('/api/v1/cart/items', [
        'spare_part_id' => $this->sparePart->id,
        'qty' => 2,
    ]);


    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'items',
            ],
        ]);
});


test('customer can checkout', function () {
    Sanctum::actingAs($this->user);


    // Add item to cart first
    $this->postJson('/api/v1/cart/items', [
        'spare_part_id' => $this->sparePart->id,
        'qty' => 1,
    ]);


    $response = $this->postJson('/api/v1/checkout', [
        'shipping_method' => 'pickup',
    ]);


    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'code',
                'total_idr',
            ],
        ]);
});


// ── Customer Tests ──


test('customer can get vehicles list', function () {
    Sanctum::actingAs($this->user);


    $response = $this->getJson('/api/v1/customer/vehicles');


    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
            'meta',
        ]);
});


test('customer can create vehicle', function () {
    Sanctum::actingAs($this->user);


    $response = $this->postJson('/api/v1/customer/vehicles', [
        'brand' => 'Lamborghini',
        'model' => 'Huracán',
        'year' => 2024,
        'plate_number' => 'B 1234 AU',
        'color' => 'Giallo Orion',
    ]);


    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'brand',
                'model',
            ],
        ]);
});


test('customer can get loyalty info', function () {
    Sanctum::actingAs($this->user);


    $response = $this->getJson('/api/v1/customer/loyalty');


    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'total_points',
                'transactions',
            ],
        ]);
});


// ── Authorization Tests ──


test('unauthenticated user cannot create offer', function () {
    $response = $this->postJson('/api/v1/offers', [
        'car_unit_id' => $this->car->id,
        'amount_idr' => 900000000,
    ]);


    $response->assertStatus(401);
});


test('unauthenticated user cannot create service booking', function () {
    $response = $this->postJson('/api/v1/service-bookings', [
        'customer_vehicle_id' => 1,
        'service_type' => 'regular_service',
        'preferred_date' => now()->addDays(3)->format('Y-m-d'),
        'preferred_time' => '09:00',
    ]);


    $response->assertStatus(401);
});


test('unauthenticated user cannot checkout', function () {
    $response = $this->postJson('/api/v1/checkout', [
        'shipping_method' => 'pickup',
    ]);


    $response->assertStatus(401);
});



