<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\Promotion;
use App\Models\PurchaseOrder;
use App\Models\SparePart;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Voucher;
use App\Services\LoyaltyService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Data pendukung demo: voucher, promo, tiket, pesan kontak, dan purchase order.
 * Hanya mengisi tabel yang masih kosong, jadi aman dijalankan sendiri:
 * php artisan db:seed --class=DemoSupportDataSeeder
 */
class DemoSupportDataSeeder extends Seeder
{
    private Carbon $today;

    public function run(): void
    {
        $this->today = Carbon::now();
        mt_srand(2026);

        DB::transaction(function () {
            Voucher::exists() || $this->vouchers();
            Promotion::exists() || $this->promotions();
            SupportTicket::exists() || $this->tickets();
            ContactMessage::exists() || $this->contactMessages();
            PurchaseOrder::exists() || $this->purchaseOrders();
        });

        Carbon::setTestNow();
    }

    private function vouchers(): void
    {
        Carbon::setTestNow($this->today->copy()->subMonths(10));

        $rows = [
            // kode, tipe, nilai, poin, min belanja, kuota, berlaku s/d, aktif, deskripsi
            ['SERVIS100', 'fixed', 100_000, 100, 1_000_000, 100, $this->today->copy()->addMonths(6), true, 'Potongan servis Rp 100.000'],
            ['PART10', 'percent', 10, 250, 500_000, 50, $this->today->copy()->addMonths(4), true, 'Diskon 10% suku cadang'],
            ['DETAILING500', 'fixed', 500_000, 500, 3_000_000, 25, $this->today->copy()->addMonths(3), true, 'Voucher detailing Rp 500.000'],
            ['WELCOME50', 'fixed', 50_000, 0, 250_000, 200, $this->today->copy()->addYear(), true, 'Voucher selamat datang'],
            ['LEBARAN2026', 'percent', 15, 0, 1_000_000, 100, Carbon::create(2026, 4, 30, 23, 59), false, 'Promo Lebaran 2026 (sudah berakhir)'],
        ];

        foreach ($rows as [$code, $type, $value, $points, $minimum, $quota, $expires, $active, $description]) {
            Voucher::create([
                'code' => $code,
                'type' => $type,
                'value' => $value,
                'points_cost' => $points,
                'min_purchase_idr' => $minimum,
                'max_usage' => $quota,
                'used_count' => 0,
                'expires_at' => $expires,
                'is_active' => $active,
                'description' => $description,
            ]);
        }

        // Beberapa customer sudah menukar poinnya
        $loyalty = app(LoyaltyService::class);
        foreach ([['customer1', 'SERVIS100', 70], ['customer2', 'PART10', 40], ['customer4', 'DETAILING500', 12]] as [$account, $code, $daysAgo]) {
            $customer = $this->customer($account);
            $voucher = Voucher::where('code', $code)->firstOrFail();
            if (! $customer || $customer->loyalty_points < $voucher->points_cost) {
                continue;
            }

            Carbon::setTestNow($this->today->copy()->subDays($daysAgo));
            $loyalty->redeemPoints($customer, $voucher->points_cost, "Tukar voucher {$code}", Voucher::class, $voucher->id);
            $voucher->redemptions()->create(['customer_id' => $customer->id, 'redeemed_at' => now()]);
            $voucher->increment('used_count');
        }
    }

    private function promotions(): void
    {
        $rows = [
            ['Promo Awal Tahun 2025', 'percent', 10, '2025-01-02', '2025-01-31', 'Servis Berkala'],
            ['Merdeka Service Week 2025', 'percent', 17, '2025-08-10', '2025-08-24', 'Semua Layanan'],
            ['Year End Part Sale 2025', 'percent', 12, '2025-12-01', '2025-12-31', 'Spare Part'],
            ['Cek AC Gratis Freon', 'fixed', 250_000, '2026-06-01', '2026-06-30', 'Servis AC'],
            ['Year End Performance 2026', 'percent', 15, $this->today->copy()->subDays(10)->toDateString(), $this->today->copy()->addDays(35)->toDateString(), 'Spare Part'],
            ['Servis Awal Tahun 2027', 'fixed', 300_000, $this->today->copy()->addDays(40)->toDateString(), $this->today->copy()->addDays(70)->toDateString(), 'Servis Berkala'],
        ];

        foreach ($rows as [$name, $type, $value, $start, $end, $target]) {
            Carbon::setTestNow(Carbon::parse($start)->subDays(7)->min($this->today));
            Promotion::create([
                'name' => $name,
                'type' => $type,
                'value' => $value,
                'starts_at' => Carbon::parse($start)->startOfDay(),
                'ends_at' => Carbon::parse($end)->endOfDay(),
                'is_active' => true,
                'description' => "{$name} untuk pelanggan AUDrive.",
                'applicable_to' => $target,
            ]);
        }
    }

    private function tickets(): void
    {
        $sales = User::where('role', 'sales')->first();

        $rows = [
            // customer, judul, keluhan, status, prioritas, hari lalu, balasan staf
            ['customer1', 'Invoice servis belum terkirim', 'Mohon dikirim ulang invoice servis bulan lalu.', 'resolved', 'low', 120, 'Invoice bisa diunduh dari menu Invoice Anda.'],
            ['customer2', 'Estimasi pengiriman suku cadang', 'Kapan pesanan saya dikirim?', 'closed', 'medium', 75, 'Sudah dikirim hari ini, mohon ditunggu.'],
            ['customer3', 'Jadwal inspeksi titip jual', 'Kapan unit titipan saya diinspeksi?', 'in_progress', 'medium', 1, 'Kami jadwalkan dengan Kepala Mekanik.'],
            ['customer4', 'AC masih kurang dingin', 'Setelah servis, AC terasa kurang dingin lagi.', 'open', 'high', 0, null],
            ['customer5', 'Pertanyaan masa garansi', 'Berapa lama garansi jasa servis?', 'open', 'low', 2, null],
        ];

        foreach ($rows as [$account, $subject, $description, $status, $priority, $daysAgo, $reply]) {
            $customer = $this->customer($account);
            if (! $customer) {
                continue;
            }

            $openedAt = $this->today->copy()->subDays($daysAgo)->subHours(4);
            Carbon::setTestNow($openedAt);
            $ticket = SupportTicket::create([
                'customer_id' => $customer->id,
                'subject' => $subject,
                'description' => $description,
                'status' => $status,
                'priority' => $priority,
                'assigned_to' => $reply ? $sales?->id : null,
                'resolved_at' => in_array($status, ['resolved', 'closed'], true) ? $openedAt->copy()->addHours(3) : null,
            ]);
            $ticket->messages()->create(['user_id' => $customer->user_id, 'sender_type' => 'customer', 'message' => $description]);

            if ($reply) {
                Carbon::setTestNow($openedAt->copy()->addHours(2));
                $ticket->messages()->create(['user_id' => $sales?->id, 'sender_type' => 'staff', 'message' => $reply]);
            }
        }
    }

    private function contactMessages(): void
    {
        $sales = User::where('role', 'sales')->first();

        $rows = [
            // nama, topik, pesan, status, hari lalu
            ['Bambang Sutrisno', 'sales', 'Apakah Porsche 911 Turbo S masih tersedia? Bisa test drive?', 'replied', 95],
            ['Citra Maharani', 'workshop', 'Saya ingin booking servis berkala untuk BMW M4.', 'replied', 48],
            ['Daniel Wirawan', 'parts', 'Apakah ready stok kampas rem untuk Audi RS5?', 'read', 9],
            ['Felicia Tan', 'other', 'Apakah AUDrive membuka kerja sama untuk acara komunitas?', 'unread', 3],
            ['Gilang Saputra', 'sales', 'Mohon info simulasi pembelian Lamborghini Urus.', 'unread', 1],
            ['Hana Oktaviani', 'workshop', 'Berapa estimasi biaya pengecatan satu panel?', 'unread', 0],
        ];

        foreach ($rows as $index => [$name, $topic, $message, $status, $daysAgo]) {
            $sentAt = $this->today->copy()->subDays($daysAgo)->subHours(3);
            Carbon::setTestNow($sentAt);
            ContactMessage::create([
                'name' => $name,
                'phone' => '0812900000'.$index,
                'email' => Str::slug($name, '.').'@example.com',
                'topic' => $topic,
                'message' => $message,
                'status' => $status,
                'read_at' => $status === 'unread' ? null : $sentAt->copy()->addHour(),
                'reply' => $status === 'replied' ? 'Terima kasih, tim kami sudah menghubungi Anda lewat WhatsApp.' : null,
                'replied_by' => $status === 'replied' ? $sales?->id : null,
                'replied_at' => $status === 'replied' ? $sentAt->copy()->addHours(2) : null,
            ]);
        }
    }

    /**
     * Pembelian stok bulanan sekitar 55% dari omzet servis + spare part bulan itu.
     * Stok saat ini tidak diubah; hanya PO berstatus "dikirim" yang menambah stok saat diterima.
     */
    private function purchaseOrders(): void
    {
        $inventory = User::where('role', 'inventory')->first();
        $parts = SparePart::orderBy('id')->get();
        if (! $inventory || $parts->isEmpty()) {
            return;
        }

        $suppliers = ['PT Brembo Indonesia', 'CV Motul Sentra Oli', 'PT Eurasia Performance Parts', 'PT Sinar Otomotif Jaya'];

        for ($month = Carbon::create(2025, 1, 1); $month->lte($this->today); $month->addMonth()) {
            $revenue = (int) DB::table('sales_orders')
                ->where('status', 'paid')
                ->where('type', '!=', 'car_sales')
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->sum('subtotal_idr');
            $orderedAt = $month->copy()->day(2)->setTime(9, 0);

            if ($revenue > 0 && $orderedAt->lt($this->today)) {
                $this->purchaseOrder($suppliers[$month->month % 4], $parts, (int) ($revenue * 0.55), 'received', $orderedAt, $inventory);
            }
        }

        // Untuk pengetesan Inventory: satu menunggu diterima, satu masih draft
        $this->purchaseOrder($suppliers[0], $parts, 20_000_000, 'sent', $this->today->copy()->subDays(2), $inventory);
        $this->purchaseOrder($suppliers[1], $parts, 8_000_000, 'draft', $this->today->copy()->subHours(5), $inventory);
    }

    private function purchaseOrder(string $supplier, $parts, int $budget, string $status, Carbon $at, User $creator): void
    {
        Carbon::setTestNow($at);

        // Modal diperkirakan 70% harga jual; barang mahal hanya dibeli bila anggarannya cukup
        $cost = fn (SparePart $part) => (int) round($part->price_idr * 0.7, -3);
        $affordable = $parts->filter(fn ($part) => $cost($part) <= $budget / 2)->values();
        $affordable = $affordable->isEmpty() ? $parts->sortBy('price_idr')->take(1)->values() : $affordable;
        $picked = $affordable->count() > 2
            ? $affordable->slice(mt_rand(0, $affordable->count() - 3), 3)->values()
            : $affordable;

        $items = $picked->map(function (SparePart $part) use ($cost, $budget, $picked, $status) {
            $qty = max(1, intdiv(intdiv($budget, $picked->count()), max(1, $cost($part))));

            return [
                'spare_part_id' => $part->id,
                'qty' => $qty,
                'cost_idr' => $cost($part),
                'subtotal_idr' => $qty * $cost($part),
                'received_qty' => $status === 'received' ? $qty : 0,
            ];
        });

        $order = PurchaseOrder::create([
            'code' => 'PO-'.$at->format('Ymd').'-'.strtoupper(Str::random(6)),
            'supplier_name' => $supplier,
            'status' => $status,
            'total_idr' => $items->sum('subtotal_idr'),
            'ordered_at' => $status === 'draft' ? null : $at,
            'received_at' => $status === 'received' ? $at->copy()->addDays(3)->min($this->today) : null,
            'created_by' => $creator->id,
            'notes' => 'Pembelian stok rutin.',
        ]);
        $order->items()->createMany($items->all());
    }

    private function customer(string $account): ?Customer
    {
        return Customer::whereHas('user', fn ($q) => $q->where('email', "{$account}@gmail.com"))->first();
    }
}
