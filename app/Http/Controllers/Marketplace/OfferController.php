<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketplace;

use App\Enums\CarUnitStatus;
use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Models\CarUnit;
use App\Models\Offer;
use App\Services\Notification\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): View
    {
        $customer = $request->user()?->customer;
        $offers = $customer
            ? Offer::where('customer_id', $customer->id)->with('carUnit.primaryPhoto')->latest()->paginate(10)
            : collect();

        return view('marketplace.offers', compact('offers'));
    }

    public function store(Request $request, CarUnit $car): RedirectResponse
    {
        $request->validate([
            'amount_idr' => 'nullable|numeric|min:1000000',
            'note' => 'nullable|string|max:500',
        ]);

        if (! $car->status || $car->status !== CarUnitStatus::Available) {
            return back()->withErrors(['car_unit' => 'Unit tidak tersedia untuk dipesan.']);
        }

        $customer = $request->user()?->customer;
        if (! $customer) {
            return back()->with('error', 'Silakan lengkapi profil customer Anda terlebih dahulu.');
        }

        $amount = (int) ($request->amount_idr ?: $car->price_sell_idr);

        $offer = DB::transaction(function () use ($car, $customer, $amount, $request) {
            // Ketika customer memesan mobil, status unit mobil langsung berubah menjadi Dipesan (Reserved)
            $car->update(['status' => CarUnitStatus::Reserved]);

            return Offer::create([
                'car_unit_id' => $car->id,
                'customer_id' => $customer->id,
                'amount_idr' => $amount,
                'status' => OfferStatus::Pending,
                'note' => $request->note,
                'expires_at' => now()->addDays(7),
            ]);
        });

        $carName = "{$car->brand} {$car->model}";

        // 1. Notifikasi ke Tim Sales & Super Admin
        $this->notificationService->notifyRoles(['sales', 'super-admin'], 'offer_submitted', [
            'title' => "Pesanan Pembelian Mobil Masuk: {$carName}",
            'customer_name' => $request->user()->name,
            'car_name' => $carName,
            'amount' => $offer->amount_idr,
            'action_url' => route('admin.offers.show', $offer->id),
        ]);

        // 2. Notifikasi ke Customer pembuat pesanan
        $this->notificationService->send($request->user(), 'offer_submitted', [
            'title' => "Pesanan Mobil Berhasil Dibuat: {$carName}",
            'message' => "Pesanan pembelian Anda untuk {$carName} seharga Rp ".number_format($offer->amount_idr, 0, ',', '.').' berhasil dibuat. Unit telah di-reserve untuk Anda, menunggu konfirmasi tim Sales.',
            'car_name' => $carName,
            'amount' => $offer->amount_idr,
            'action_url' => route('marketplace.show', $car->slug),
        ]);

        return redirect()
            ->route('marketplace.show', $car->slug)
            ->with('success', 'Pesanan pembelian unit mobil berhasil dibuat! Unit kini berstatus Dipesan (Reserved) untuk Anda.');
    }

    public function update(Request $request, Offer $offer): RedirectResponse
    {
        $customer = $request->user()?->customer;
        if ($offer->customer_id !== $customer?->id) {
            abort(403);
        }

        $request->validate([
            'amount_idr' => 'required|numeric|min:1000000',
            'note' => 'nullable|string|max:500',
        ]);

        $offer->update([
            'amount_idr' => (int) $request->amount_idr,
            'note' => $request->note,
        ]);

        return back()->with('success', 'Pesanan berhasil diperbarui.');
    }

    public function destroy(Request $request, Offer $offer): RedirectResponse
    {
        $customer = $request->user()?->customer;
        if ($offer->customer_id !== $customer?->id) {
            abort(403);
        }

        DB::transaction(function () use ($offer) {
            // Jika pesanan yang dibatalkan berstatus pending, kembalikan status unit mobil menjadi Available
            if ($offer->status === OfferStatus::Pending && $offer->carUnit?->status === CarUnitStatus::Reserved) {
                $offer->carUnit->update(['status' => CarUnitStatus::Available]);
            }

            $offer->delete();
        });

        return back()->with('success', 'Pesanan berhasil dibatalkan dan unit mobil kembali berstatus Tersedia.');
    }
}
