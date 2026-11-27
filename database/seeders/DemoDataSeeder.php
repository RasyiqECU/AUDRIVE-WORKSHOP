<?php

namespace Database\Seeders;

use App\Enums\CarUnitStatus;
use App\Enums\WorkOrderStatus;
use App\Models\Cart;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\ServiceBooking;
use App\Models\SparePart;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Services\ApprovalService;
use App\Services\CarBookingService;
use App\Services\CartService;
use App\Services\CarUnitService;
use App\Services\CheckoutService;
use App\Services\ConsignmentService;
use App\Services\Notification\Contracts\NotificationServiceContract;
use App\Services\Notification\NotificationService;
use App\Services\PaymentVerificationService;
use App\Services\PosService;
use App\Services\WarrantyClaimService;
use App\Services\WorkOrderService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Riwayat transaksi Januari 2025 s/d hari ini, dibuat lewat service aplikasi
 * (waktu digeser per transaksi) supaya angka, status, dan invoice konsisten.
 * Dipanggil lewat `php artisan demo:reset`.
 */
class DemoDataSeeder extends Seeder
{
    /** Foto asli di public/images untuk tiap unit katalog (id => file). */
    private const CAR_IMAGES = [
        1 => ['636266983p.jpg', 'images.jpg'],
        2 => ['images (1).jpg'],
        3 => ['lamborghini huracan evo.jpg'],
        4 => ['ferrari f8 tributo.jpg'],
        5 => ['porsche 911 turbo s.jpg'],
        6 => ['lamborghini urus.jpg'],
        7 => ['mclaren-720s-hero.jpg'],
        8 => ['bmwm8_c.jpg'],
        9 => ['mercedes benz amg gt 63 s.jpg'],
        10 => ['audi r8 v10 plus.jpg'],
        11 => ['porsche cayenne turbo gt.jpg'],
        12 => ['ferrari roma.jpg'],
    ];

    private const PART_IMAGES = [
        'AUD-BRK-001' => 'kampas rem belakang cramic brembo.jpg',
        'AUD-FLT-105' => 'oem high flow oil filter supercar v8.jpg',
        'AUD-IGN-002' => 'busi iridium racing ngk laser spark plug.jpg',
        'AUD-OIL-300' => 'oil mesin motul 300v trophy 0w-40( 2L).jpg',
        'AUD-SUS-501' => 'coilover kit kw suspension variant 3.jpg',
        'AUD-EXH-770' => 'titanium slip on exhaust akropovic evolution.jpg',
        'AUD-INT-102' => 'eventuri karbon fiber cold air intake system.jpg',
        'AUD-BRK-990' => 'brembo carbon ceramic brakes rotor 390 mm.jpg',
    ];

    private const JOBS = [
        'regular_service' => ['Servis berkala & pengecekan 40 titik', 1_500_000],
        'heavy_repair' => ['Overhaul sistem rem dan kaki-kaki', 6_500_000],
        'body_repair' => ['Perbaikan bodi dan pengecatan panel', 4_800_000],
        'ac_service' => ['Servis AC dan isi freon', 1_200_000],
        'inspection' => ['Inspeksi menyeluruh dan diagnosa komputer', 900_000],
    ];

    private Carbon $today;

    /** @var array<string, User> */
    private array $staff = [];

    /** @var list<Customer> */
    private array $customers = [];

    /** Spare part yang wajar dipakai rutin (id). @var list<int> */
    private array $consumables = [];

    private int $odometer = 8_000;

    public function run(): void
    {
        $this->today = Carbon::now();
        mt_srand(2025);
        $this->muteNotifications();

        Carbon::setTestNow(Carbon::create(2024, 12, 20, 9));
        $this->call(UserSeeder::class);
        Carbon::setTestNow();

        foreach (User::all() as $user) {
            $this->staff[Str::before($user->email, '@')] = $user;
        }
        $this->customers = Customer::orderBy('id')->get()->all();

        $this->linkImages();

        // Riwayat tidak boleh mengubah angka stok saat ini
        $stocks = SparePart::withTrashed()->get(['id', 'sku', 'stock', 'status']);
        DB::table('spare_parts')->update(['stock' => 5000]);
        $this->consumables = $stocks->whereIn('sku', ['AUD-IGN-002', 'AUD-OIL-300', 'AUD-FLT-105', 'AUD-BRK-001'])->pluck('id')->all()
            ?: $stocks->pluck('id')->all();

        $this->carHistory();
        $this->monthlyHistory();
        $this->currentWork();

        Carbon::setTestNow();
        Auth::logout();

        foreach ($stocks as $part) {
            $booked = (int) DB::table('spare_parts')->where('id', $part->id)->value('booked_stock');
            DB::table('spare_parts')->where('id', $part->id)->update([
                'stock' => max((int) $part->stock, $booked),
                'status' => $part->getRawOriginal('status'),
            ]);
        }

        $this->alignDocumentDates();
        $this->call(DemoSupportDataSeeder::class);
    }

    /** Ribuan notifikasi lama tidak berguna dan mengotori log. */
    private function muteNotifications(): void
    {
        $silent = new class extends NotificationService
        {
            public function send(User $user, string $type, array $data = []): bool
            {
                return true;
            }

            public function sendVia(string $channel, User $user, string $type, array $data = []): bool
            {
                return true;
            }

            public function sendToMany(array $users, string $type, array $data = []): array
            {
                return [];
            }

            public function notifyRoles(array $roles, string $type, array $data = []): array
            {
                return [];
            }
        };

        app()->instance(NotificationService::class, $silent);
        app()->instance(NotificationServiceContract::class, $silent);
    }

    /** Salin foto asli ke disk publik dengan nama yang bisa dikenali. */
    private function linkImages(): void
    {
        foreach (self::CAR_IMAGES as $carId => $files) {
            if (! CarUnit::withTrashed()->whereKey($carId)->exists()) {
                continue;
            }

            $paths = array_map(fn ($file) => $this->copyImage($file, "cars/{$carId}"), $files);
            $old = DB::table('car_photos')->where('car_unit_id', $carId)->pluck('path')->all();
            DB::table('car_photos')->where('car_unit_id', $carId)->delete();
            Storage::disk('public')->delete(array_values(array_diff($old, $paths)));

            $this->attachPhotos($carId, $paths);
        }

        foreach (self::PART_IMAGES as $sku => $file) {
            $part = SparePart::withTrashed()->where('sku', $sku)->first();
            if (! $part) {
                continue;
            }

            $path = $this->copyImage($file, "spare-parts/{$part->id}");
            if ($part->image && $part->image !== $path) {
                Storage::disk('public')->delete($part->image);
            }
            DB::table('spare_parts')->where('id', $part->id)->update(['image' => $path]);
        }
    }

    private function copyImage(string $file, string $folder): string
    {
        $path = "{$folder}/{$file}";
        Storage::disk('public')->put($path, file_get_contents(public_path("images/{$file}")));

        return $path;
    }

    /** @param list<string> $paths */
    private function attachPhotos(int $carId, array $paths): void
    {
        foreach ($paths as $order => $path) {
            DB::table('car_photos')->insert([
                'car_unit_id' => $carId,
                'path' => $path,
                'is_primary' => $order === 0,
                'sort_order' => $order,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // ── Penjualan unit ──

    private function carHistory(): void
    {
        DB::table('car_units')->whereNull('deleted_at')->update(['status' => CarUnitStatus::Available->value]);

        // Unit katalog yang berstatus terjual mendapat riwayat penjualannya
        $catalogSales = [[4, '2025-03-14', 0], [9, '2025-09-09', 2], [10, '2026-02-18', 3], [1, '2026-08-05', 1]];
        foreach ($catalogSales as [$carId, $date, $buyer]) {
            if ($car = CarUnit::find($carId)) {
                $this->sellCar($car, $this->customers[$buyer], Carbon::parse($date)->setTime(11, 0));
            }
        }

        // Unit lain yang sudah lama terjual: diarsipkan supaya katalog tidak berubah
        $dealer = [
            ['Porsche', '718 Cayman GT4', 2022, 2_650_000_000, 'porsche 911 turbo s.jpg', '2025-01-21', 1],
            ['Ferrari', '488 Pista', 2020, 6_900_000_000, 'ferrari f8 tributo.jpg', '2025-06-11', 3],
            ['BMW', 'M5 CS', 2023, 3_100_000_000, 'bmwm8_c.jpg', '2025-12-03', 4],
            ['Audi', 'RS e-tron GT', 2024, 2_900_000_000, 'audi r8 v10 plus.jpg', '2026-05-22', 0],
        ];
        foreach ($dealer as [$brand, $model, $year, $price, $image, $date, $buyer]) {
            $at = Carbon::parse($date)->setTime(13, 0);
            Carbon::setTestNow($at->copy()->subDays(20));

            $car = app(CarUnitService::class)->create([
                'brand' => $brand,
                'model' => $model,
                'year' => $year,
                'transmission' => 'automatic',
                'color' => 'Hitam',
                'mileage_km' => mt_rand(500, 9000),
                'price_buy_idr' => (int) ($price * 0.88),
                'price_sell_idr' => $price,
                'status' => CarUnitStatus::Available,
                'condition' => 'used',
                'description' => 'Unit arsip penjualan (data demo).',
            ]);
            $this->attachPhotos($car->id, [$this->copyImage($image, "cars/{$car->id}")]);

            $this->sellCar($car, $this->customers[$buyer], $at);
            $car->delete();
        }

        $consignments = [
            ['Lamborghini', 'Gallardo LP560-4', 2013, 3_400_000_000, 'lamborghini huracan evo.jpg', '2025-04-08', 2, 4],
            ['Mercedes-Benz', 'SLS AMG', 2014, 5_200_000_000, 'mercedes benz amg gt 63 s.jpg', '2026-03-10', 4, 1],
        ];
        foreach ($consignments as [$brand, $model, $year, $price, $image, $date, $seller, $buyer]) {
            $at = Carbon::parse($date)->setTime(10, 0);
            Carbon::setTestNow($at->copy()->subDays(25));
            $car = $this->submitConsignment($this->customers[$seller], $brand, $model, $year, $price, $image);

            $this->actAs('mekanik1');
            app(ConsignmentService::class)->inspect($car, $this->staff['mekanik1'], [
                'result' => 'pass', 'notes' => 'Kondisi layak jual.', 'estimated_repair_idr' => 15_000_000,
            ]);
            $this->actAs('manager');
            app(ConsignmentService::class)->approve($car->fresh(), $this->staff['manager'], [
                'price_sell_idr' => $price, 'commission_idr' => (int) ($price * 0.03), 'repair_cost_idr' => 15_000_000,
            ]);

            $this->sellCar($car->fresh(), $this->customers[$buyer], $at);

            Carbon::setTestNow($at->copy()->addDays(3));
            $this->actAs('kasir');
            app(ConsignmentService::class)->paySeller($car->fresh(), $this->staff['kasir']);
            $car->fresh()->delete();
        }
    }

    private function submitConsignment(Customer $seller, string $brand, string $model, int $year, int $price, string $image): CarUnit
    {
        Auth::setUser($seller->user);

        $car = app(ConsignmentService::class)->submit($seller, [
            'brand' => $brand,
            'model' => $model,
            'year' => $year,
            'transmission' => 'automatic',
            'color' => 'Merah',
            'mileage_km' => mt_rand(9000, 30000),
            'asking_price_idr' => $price,
            'condition_defects' => 'Baret halus di bumper depan.',
            'condition_additions' => 'Velg forged dan knalpot aftermarket.',
        ]);
        $this->attachPhotos($car->id, [$this->copyImage($image, "cars/{$car->id}")]);

        return $car;
    }

    private function sellCar(CarUnit $car, Customer $buyer, Carbon $at): void
    {
        Carbon::setTestNow($at->copy()->subDays(2));
        Auth::setUser($buyer->user);
        $order = app(CarBookingService::class)->book($buyer, $car, 'cashier', 'self_pickup');

        Carbon::setTestNow($at);
        $this->actAs('kasir');
        app(PosService::class)->payCarSalesOrder($order, 0, 'transfer', $this->staff['kasir']->id);

        DB::table('sales_orders')->where('id', $order->id)->update(['handed_over_at' => $at->copy()->addDay()]);
    }

    // ── Servis & spare part bulanan ──

    private function monthlyHistory(): void
    {
        $month = Carbon::create(2025, 1, 1);

        for ($index = 0; $month->lte($this->today); $index++, $month->addMonth()) {
            $isCurrent = $month->isSameMonth($this->today);
            $lastDay = $isCurrent ? max(1, $this->today->day - 3) : 26;
            $count = mt_rand(3, 4) + intdiv($index, 8);

            $days = [];
            for ($i = 0; $i < $count; $i++) {
                $days[] = mt_rand(1, $lastDay);
            }
            sort($days);

            // Minimal satu servis dan satu penjualan part, dari minimal dua customer
            $kinds = ['service', 'part_cashier', 'part_transfer'];
            $firstCustomer = mt_rand(0, 4);

            foreach ($days as $i => $day) {
                $kind = $kinds[$i] ?? ['service', 'service', 'part_cashier', 'part_transfer', 'pos'][mt_rand(0, 4)];
                $customer = $this->customers[$i < 2 ? ($firstCustomer + $i) % 5 : mt_rand(0, 4)];
                $at = $month->copy()->day($day)->setTime(mt_rand(9, 11), mt_rand(0, 59));

                match ($kind) {
                    'service' => $this->paidService($customer, $at, $index),
                    'part_cashier' => $this->paidPartOrder($customer, $at, 'cashier'),
                    'part_transfer' => $this->paidPartOrder($customer, $at, 'online_transfer'),
                    default => $this->posSale($customer, $at),
                };
            }
        }
    }

    /** Booking → check-in → Work Order (belum dikerjakan). */
    private function openService(Customer $customer, Carbon $at, string $type, string $mechanic, int $vehicleIndex = 0): WorkOrder
    {
        Carbon::setTestNow($at);
        $this->actAs('sales');
        $vehicles = $customer->vehicles()->orderBy('id')->get();
        $vehicle = $vehicles[$vehicleIndex % $vehicles->count()];

        $booking = ServiceBooking::create([
            'customer_id' => $customer->id,
            'customer_vehicle_id' => $vehicle->id,
            'service_type' => $type,
            'preferred_date' => $at->toDateString(),
            'preferred_time' => $at->format('H:i'),
            'status' => 'checked_in',
            'estimated_cost_idr' => self::JOBS[$type][1],
            'notes' => 'Keluhan: perlu pengecekan rutin.',
            'checked_in_at' => $at,
            'checked_in_by' => $this->staff['sales']->id,
            'odometer_km' => $this->odometer += mt_rand(150, 600),
            'body_condition' => 'Mulus, tidak ada baret baru.',
            'belongings' => 'STNK dan kunci cadangan.',
        ]);

        return WorkOrder::create([
            'service_booking_id' => $booking->id,
            'customer_vehicle_id' => $vehicle->id,
            'status' => WorkOrderStatus::Queued,
            'assigned_mechanic_id' => $this->staff[$mechanic]->id,
        ]);
    }

    /** Mekanik mulai bekerja: jasa dicatat dan satu part diusulkan. */
    private function startWork(WorkOrder $workOrder, string $type, string $mechanic, int $partId): WorkOrderItem
    {
        $service = app(WorkOrderService::class);
        $this->actAs($mechanic);

        $service->updateStatus($workOrder, WorkOrderStatus::InProgress);
        $service->addServiceItem($workOrder, self::JOBS[$type][0], 1, self::JOBS[$type][1]);

        return $service->addPartItem($workOrder->fresh(), SparePart::findOrFail($partId), mt_rand(1, 2));
    }

    private function finishWork(WorkOrder $workOrder, WorkOrderItem $part, string $mechanic): void
    {
        $service = app(WorkOrderService::class);

        $this->actAs('inventory');
        $service->markPartChecked($part);
        $this->actAs('sales');
        $service->decidePart($part->fresh(), 'buy');

        $this->actAs($mechanic);
        $service->updateStatus($workOrder->fresh(), WorkOrderStatus::QualityControl);
        $service->complete($workOrder->fresh());
    }

    private function completedService(Customer $customer, Carbon $at, int $index, int $vehicleIndex = 0): WorkOrder
    {
        $type = array_keys(self::JOBS)[mt_rand(0, count(self::JOBS) - 1)];
        $mechanic = 'mekanik'.mt_rand(1, 2);
        $workOrder = $this->openService($customer, $at, $type, $mechanic, $vehicleIndex);
        $part = $this->startWork($workOrder, $type, $mechanic, $this->consumables[mt_rand(0, count($this->consumables) - 1)]);

        Carbon::setTestNow($at->copy()->addHours(5));
        $this->finishWork($workOrder, $part, $mechanic);
        DB::table('work_orders')->where('id', $workOrder->id)->update(['started_at' => $at, 'notes' => null]);

        return $workOrder->fresh();
    }

    private function paidService(Customer $customer, Carbon $at, int $index): void
    {
        $workOrder = $this->completedService($customer, $at, $index, $index);

        Carbon::setTestNow($at->copy()->addHours(6));
        $this->actAs('kasir');
        app(PosService::class)->createWorkOrderPayment($workOrder, 0, ['cash', 'edc', 'transfer'][mt_rand(0, 2)], $this->staff['kasir']->id);

        Carbon::setTestNow($at->copy()->addHours(7));
        $this->actAs('sales');
        app(WorkOrderService::class)->deliver($workOrder->fresh());
    }

    private function checkout(Customer $customer, Carbon $at, string $channel): SalesOrder
    {
        Carbon::setTestNow($at);
        Auth::setUser($customer->user);

        $cart = Cart::firstOrCreate(['customer_id' => $customer->id], ['session_id' => null]);
        $parts = SparePart::orderBy('id')->get();
        foreach (range(1, mt_rand(1, 2)) as $ignored) {
            $part = $parts[mt_rand(0, $parts->count() - 1)];
            app(CartService::class)->addItem($cart, $part, $part->price_idr > 5_000_000 ? 1 : mt_rand(1, 3));
        }

        $delivery = mt_rand(0, 2) === 0;

        return app(CheckoutService::class)->processCheckout(
            $cart->fresh(), $customer, $delivery ? 'delivery' : 'pickup', $delivery ? $customer->address : null, $channel
        );
    }

    private function uploadProof(SalesOrder $order, Customer $customer): Payment
    {
        Auth::setUser($customer->user);
        $file = tempnam(sys_get_temp_dir(), 'bukti');
        $image = imagecreatetruecolor(420, 220);
        imagefill($image, 0, 0, imagecolorallocate($image, 245, 245, 245));
        $ink = imagecolorallocate($image, 20, 20, 20);
        foreach (['BUKTI TRANSFER (CONTOH DATA DEMO)', 'Pengirim : '.$customer->user->name, 'Pesanan  : '.$order->code,
            'Nominal  : Rp '.number_format($order->amountDue(), 0, ',', '.')] as $line => $text) {
            imagestring($image, 5, 20, 30 + $line * 40, Str::ascii($text), $ink);
        }
        imagepng($image, $file);

        return app(PaymentVerificationService::class)->submitProof($order, [
            'sender_name' => $customer->user->name,
            'destination_bank' => 'BCA',
            'amount_idr' => $order->amountDue(),
        ], new UploadedFile($file, 'bukti-transfer.png', 'image/png', null, true));
    }

    private function paidPartOrder(Customer $customer, Carbon $at, string $channel): void
    {
        $order = $this->checkout($customer, $at, $channel);

        if ($channel === 'online_transfer') {
            Carbon::setTestNow($at->copy()->addHour());
            $proof = $this->uploadProof($order, $customer);
            Carbon::setTestNow($at->copy()->addHours(3));
            $this->actAs('kasir');
            app(PaymentVerificationService::class)->approve($proof, $this->staff['kasir']);
        } else {
            Carbon::setTestNow($at->copy()->addHours(2));
            $this->actAs('kasir');
            app(CheckoutService::class)->processPayment($order, 'cash', $order->total_idr);
        }

        DB::table('sales_orders')->where('id', $order->id)->update([
            'prepared_at' => $at->copy()->addHours(5),
            'handed_over_at' => $at->copy()->addDay(),
        ]);
    }

    private function posSale(Customer $customer, Carbon $at): void
    {
        Carbon::setTestNow($at);
        $this->actAs('kasir');

        app(PosService::class)->createPosTransaction(
            [['spare_part_id' => $this->consumables[mt_rand(0, count($this->consumables) - 1)], 'qty' => mt_rand(1, 4)]],
            $customer, 0, 'cash', $this->staff['kasir']->id
        );
    }

    // ── Pekerjaan yang masih berjalan (untuk pengetesan) ──

    private function currentWork(): void
    {
        $ago = fn (int $days, int $hour = 9) => $this->today->copy()->subDays($days)->setTime($hour, 0)->min($this->today->copy()->subMinutes(30));
        [$c1, $c2, $c3, $c4, $c5] = $this->customers;

        // Servis: dikerjakan dan menunggu konfirmasi part
        $workOrder = $this->openService($c1, $ago(1), 'regular_service', 'mekanik1');
        $this->actAs('mekanik1');
        app(WorkOrderService::class)->updateStatus($workOrder, WorkOrderStatus::InProgress);
        app(WorkOrderService::class)->addServiceItem($workOrder->fresh(), self::JOBS['regular_service'][0], 1, self::JOBS['regular_service'][1]);
        DB::table('work_orders')->where('id', $workOrder->id)->update(['started_at' => $ago(1)]);
        $workOrder->serviceBooking->update(['status' => 'in_progress']);

        $waiting = $this->openService($c2, $ago(1, 10), 'heavy_repair', 'mekanik2');
        $part = $this->startWork($waiting, 'heavy_repair', 'mekanik2', $this->consumables[0]);
        $this->actAs('inventory');
        app(WorkOrderService::class)->markPartChecked($part);
        $waiting->serviceBooking->update(['status' => 'in_progress']);

        // Selesai QC, belum dibayar
        $unpaid = $this->completedService($c3, $ago(2), 22);

        // Booking hari ini, belum check-in
        Carbon::setTestNow($ago(0));
        ServiceBooking::create([
            'customer_id' => $c4->id,
            'customer_vehicle_id' => $c4->vehicles()->value('id'),
            'service_type' => 'ac_service',
            'preferred_date' => $this->today->toDateString(),
            'preferred_time' => '14:00',
            'status' => 'confirmed',
            'notes' => 'AC kurang dingin.',
        ]);

        // Pesanan menunggu verifikasi Cashier
        foreach ([[$c4, 1], [$c5, 0]] as [$customer, $days]) {
            $order = $this->checkout($customer, $ago($days, 10), 'online_transfer');
            $this->uploadProof($order, $customer);
        }

        // Klaim garansi: satu menunggu diagnosa, satu menunggu keputusan Manager
        foreach ([[$c5, 6, false], [$c4, 5, true]] as [$customer, $days, $diagnosed]) {
            $at = $ago($days);
            $this->paidService($customer, $at, 0);
            $source = WorkOrder::latest('id')->firstOrFail();

            Carbon::setTestNow($ago(1, 13));
            $this->actAs('sales');
            $warranty = app(WarrantyClaimService::class);
            $claim = $warranty->create($warranty->findTransaction($source->code), [
                'work_order_item_id' => $source->billableItems()->where('type', 'service')->value('id'),
                'current_km' => $source->serviceBooking->odometer_km + 120,
                'complaint' => 'Gejala yang sama muncul lagi setelah servis.',
            ], $this->staff['sales']);

            if ($diagnosed) {
                $this->actAs('mekanik1');
                $warranty->diagnose($claim, $this->staff['mekanik1'], true, 'Pemasangan perlu disetel ulang, masuk garansi.');
            }
        }

        // Titip jual menunggu inspeksi
        Carbon::setTestNow($ago(1, 11));
        $this->submitConsignment($c3, 'Ferrari', 'California T', 2017, 4_100_000_000, 'ferrari roma.jpg');

        // Diskon menunggu persetujuan Manager
        Carbon::setTestNow($ago(0));
        $this->actAs('kasir');
        app(ApprovalService::class)->createRequest('discount', [
            'sales_order_id' => SalesOrder::where('work_order_id', $unpaid->id)->value('id'),
            'amount_idr' => 150_000,
            'reason' => 'Potongan untuk pelanggan langganan.',
        ], $this->staff['kasir']);
    }

    private function actAs(string $account): void
    {
        Auth::setUser($this->staff[$account]);
    }

    /** Nomor dokumen memuat tanggal; samakan dengan tanggal transaksinya. */
    private function alignDocumentDates(): void
    {
        $redate = fn (string $number, string $date) => preg_replace('/-\d{8}-/', '-'.Carbon::parse($date)->format('Ymd').'-', $number);

        foreach (DB::table('sales_orders')->get(['id', 'code', 'created_at']) as $order) {
            $code = $redate($order->code, $order->created_at);
            if ($code === $order->code) {
                continue;
            }

            DB::table('sales_orders')->where('id', $order->id)->update(['code' => $code]);
            foreach (['loyalty_transactions' => 'description', 'stock_movements' => 'notes', 'audit_logs' => 'description'] as $table => $column) {
                DB::table($table)->where($column, 'like', "%{$order->code}%")
                    ->update([$column => DB::raw("REPLACE(`{$column}`, ".DB::getPdo()->quote($order->code).', '.DB::getPdo()->quote($code).')')]);
            }
        }

        foreach (DB::table('invoices')->get(['id', 'number', 'created_at']) as $invoice) {
            DB::table('invoices')->where('id', $invoice->id)->update(['number' => $redate($invoice->number, $invoice->created_at)]);
        }

        DB::table('notifications')->delete();
    }
}
