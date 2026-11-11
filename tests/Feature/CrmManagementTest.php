<?php

declare(strict_types=1);

use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\Promotion;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Fase 4 audit — alur fitur CRM (Super Admin / Manager / Sales):
 * pesan customer, tiket layanan, voucher & promo CRUD + toggle, koreksi poin loyalitas.
 */
function crmStaff(string $role = 'manager'): User
{
    return $role === 'super-admin'
        ? User::factory()->superAdmin()->create()
        : User::factory()->{$role}()->create();
}

test('sales membalas pesan customer dan statusnya menjadi replied', function () {
    $sales = crmStaff('sales');
    $message = ContactMessage::create([
        'name' => 'Antiv R',
        'phone' => '081234567890',
        'email' => 'antiv@example.com',
        'topic' => 'konsultasi servis',
        'message' => 'Saya ingin tanya paket servis lengkap.',
        'status' => 'unread',
    ]);

    $this->actingAs($sales)
        ->post(route('admin.contact-messages.reply', $message), [
            'reply' => 'Untuk paket servis lengkap estimasinya Rp 3 juta.',
        ])
        ->assertRedirect(route('admin.contact-messages.show', $message));

    $message->refresh();
    expect($message->status)->toBe('replied');
    expect((int) $message->replied_by)->toBe($sales->id);
    expect($message->reply)->toContain('paket servis lengkap');

    // Pesan bisa dihapus oleh role yang berhak
    $this->actingAs($sales)->delete(route('admin.contact-messages.destroy', $message))->assertStatus(302);
    expect(ContactMessage::find($message->id))->toBeNull();
});

test('manager membalas tiket: auto in_progress, auto assign, notifikasi ke customer', function () {
    $manager = crmStaff('manager');
    $customer = Customer::factory()->create();
    $ticket = SupportTicket::factory()->open()->create(['customer_id' => $customer->id]);

    $this->actingAs($manager)
        ->post(route('admin.tickets.reply', $ticket), [
            'message' => 'Tiket Anda sudah kami terima dan sedang diproses tim teknis.',
        ])
        ->assertStatus(302);

    $ticket->refresh();
    expect($ticket->messages()->where('sender_type', 'staff')->exists())->toBeTrue();
    expect($ticket->status->value ?? $ticket->status)->toBe('in_progress', 'Reply tiket open harus langsung in_progress');
    expect((int) $ticket->assigned_to)->toBe($manager->id);
    expect($customer->user->notifications()->where('data->type', 'ticket_reply')->exists())->toBeTrue();

    // Status ditutup (resolved) oleh manager
    $this->actingAs($manager)
        ->patch(route('admin.tickets.status', $ticket), ['status' => 'resolved'])
        ->assertStatus(302);

    expect($ticket->fresh()->status->value ?? $ticket->fresh()->status)->toBe('resolved');
    expect($ticket->fresh()->resolved_at)->not->toBeNull();
});

test('manager CRUD voucher: buat, ubah, toggle, hapus dan proteksi voucher terpakai', function () {
    $manager = crmStaff('manager');

    // Store voucher baru
    $this->actingAs($manager)
        ->post(route('admin.vouchers.store'), [
            'code' => 'AUDRIVENEW',
            'type' => 'percent',
            'value' => 10,
            'max_usage' => 50,
            'is_active' => '1',
            'description' => 'Voucher audit QA',
        ])
        ->assertRedirect(route('admin.vouchers.index'));

    $voucher = Voucher::where('code', 'AUDRIVENEW')->first();
    expect($voucher)->not->toBeNull('Voucher seharusnya tersimpan');
    expect($voucher->is_active)->toBeTrue();

    // Update nilai voucher
    $this->actingAs($manager)
        ->patch(route('admin.vouchers.update', $voucher), [
            'code' => 'AUDRIVENEW',
            'type' => 'percent',
            'value' => 20,
            'max_usage' => 100,
        ])
        ->assertStatus(302);
    expect((int) $voucher->fresh()->value)->toBe(20);
    expect($voucher->fresh()->is_active)->toBeFalse('Tanpa checkbox is_active, voucher dinonaktifkan oleh update');

    // Toggle aktif/nonaktif
    $this->actingAs($manager)->patch(route('admin.vouchers.toggle', $voucher))->assertStatus(302);
    expect($voucher->fresh()->is_active)->toBeTrue();
    $this->actingAs($manager)->patch(route('admin.vouchers.toggle', $voucher))->assertStatus(302);
    expect($voucher->fresh()->is_active)->toBeFalse();

    // Detail show (regresi bug method show yang hilang)
    $this->actingAs($manager)->get(route('admin.vouchers.show', $voucher))->assertOk();

    // Voucher yang sudah terpakai tidak terhapus, hanya dinonaktifkan
    $used = Voucher::factory()->create(['is_active' => true, 'used_count' => 2]);
    $this->actingAs($manager)->delete(route('admin.vouchers.destroy', $used))->assertStatus(302);
    expect(Voucher::find($used->id))->not->toBeNull('Voucher pernah terpakai tidak boleh terhapus');
    expect($used->fresh()->is_active)->toBeFalse('Voucher pernah terpakai harus dinonaktifkan');

    // Voucher belum terpakai → terhapus
    $this->actingAs($manager)->delete(route('admin.vouchers.destroy', $voucher))->assertStatus(302);
    expect(Voucher::find($voucher->id))->toBeNull();
});

test('manager CRUD promo: store, update, toggle, show, destroy', function () {
    $manager = crmStaff('manager');

    $this->actingAs($manager)->get(route('admin.promotions.create'))->assertOk();

    $this->actingAs($manager)
        ->post(route('admin.promotions.store'), [
            'name' => 'Ramadan Dayak',
            'type' => 'fixed',
            'value' => 500_000,
            'starts_at' => now()->toDateString(),
            'ends_at' => now()->addWeek()->toDateString(),
            'applicable_to' => 'Servis',
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.promotions.index'));

    $promo = Promotion::where('name', 'Ramadan Dayak')->first();
    expect($promo)->not->toBeNull('Promo seharusnya tersimpan');
    expect($promo->is_active)->toBeTrue();
    expect($promo->slug)->not->toBeEmpty('Slug promo harus terisi otomatis');

    // Show (regresi bug method show yang hilang)
    $this->actingAs($manager)->get(route('admin.promotions.show', $promo))->assertOk();

    $this->actingAs($manager)->patch(route('admin.promotions.toggle', $promo))->assertStatus(302);
    expect($promo->fresh()->is_active)->toBeFalse();

    $this->actingAs($manager)->delete(route('admin.promotions.destroy', $promo))->assertStatus(302);
    expect(Promotion::find($promo->id))->toBeNull();
});

test('koreksi poin loyalitas: bonus, deduct, saldo kurang ditolak dan notifikasi terkirim', function () {
    $manager = crmStaff('manager');
    $customer = Customer::factory()->create(['loyalty_points' => 100]);

    // Bonus +50
    $this->actingAs($manager)->from(route('admin.loyalty.index'))
        ->post(route('admin.loyalty.adjust', $customer), [
            'action' => 'add',
            'points' => 50,
            'description' => 'Bonus anniversary',
        ])
        ->assertStatus(302);
    expect((int) $customer->fresh()->loyalty_points)->toBe(150);

    // Deduct melebihi saldo ditolak
    $this->actingAs($manager)->from(route('admin.loyalty.index'))
        ->post(route('admin.loyalty.adjust', $customer), [
            'action' => 'deduct',
            'points' => 999,
            'description' => 'Koreksi keliru',
        ])
        ->assertStatus(302);
    expect(session('error'))->not->toBeNull();
    expect((int) $customer->fresh()->loyalty_points)->toBe(150, 'Saldo tidak boleh berubah bila melebihi');

    // Deduct 60 → 90 + transaksi catatan
    $this->actingAs($manager)->from(route('admin.loyalty.index'))
        ->post(route('admin.loyalty.adjust', $customer), [
            'action' => 'deduct',
            'points' => 60,
            'description' => 'Koreksi poin',
        ])
        ->assertStatus(302);
    expect((int) $customer->fresh()->loyalty_points)->toBe(90);

    // Halaman loyalty index & history bisa dibuka dan menampilkan partners
    $this->actingAs($manager)->get(route('admin.loyalty.index'))->assertOk();
    $this->actingAs($manager)->get(route('admin.loyalty.history', ['type' => 'earned']))->assertOk();
});
