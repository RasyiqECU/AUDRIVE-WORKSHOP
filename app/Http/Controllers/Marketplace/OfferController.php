<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketplace;

use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Models\CarUnit;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferController extends Controller
{
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
            'amount_idr' => 'required|numeric|min:1000000',
            'note'       => 'nullable|string|max:500',
        ]);

        $customer = $request->user()?->customer;
        if (!$customer) {
            return back()->with('error', 'Silakan lengkapi profil customer Anda terlebih dahulu.');
        }

        Offer::create([
            'car_unit_id' => $car->id,
            'customer_id' => $customer->id,
            'amount_idr'  => (int)$request->amount_idr,
            'status'      => OfferStatus::Pending,
            'note'        => $request->note,
            'expires_at'  => now()->addDays(7),
        ]);

        return redirect()
            ->route('marketplace.show', $car->slug)
            ->with('success', 'Penawaran harga berhasil dikirimkan.');
    }

    public function update(Request $request, Offer $offer): RedirectResponse
    {
        $customer = $request->user()?->customer;
        if ($offer->customer_id !== $customer?->id) {
            abort(403);
        }

        $request->validate([
            'amount_idr' => 'required|numeric|min:1000000',
            'note'       => 'nullable|string|max:500',
        ]);

        $offer->update([
            'amount_idr' => (int)$request->amount_idr,
            'note'       => $request->note,
        ]);

        return back()->with('success', 'Penawaran berhasil diperbarui.');
    }

    public function destroy(Request $request, Offer $offer): RedirectResponse
    {
        $customer = $request->user()?->customer;
        if ($offer->customer_id !== $customer?->id) {
            abort(403);
        }

        $offer->delete();

        return back()->with('success', 'Penawaran berhasil dibatalkan.');
    }
}
