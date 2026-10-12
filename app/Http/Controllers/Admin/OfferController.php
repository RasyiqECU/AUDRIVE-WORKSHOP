<?php


declare(strict_types=1);


namespace App\Http\Controllers\Admin;


use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Services\OfferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class OfferController extends Controller
{
    public function __construct(
        protected OfferService $offerService
    ) {}


    public function index(Request $request): View
    {
        $offers = Offer::query()
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->with(['carUnit', 'customer.user', 'reviewer'])
            ->latest()
            ->paginate(15);


        return view('admin.offers.index', compact('offers'));
    }


    public function show(Offer $offer): View
    {
        $offer->load(['carUnit.photos', 'customer.user']);


        return view('admin.offers.show', compact('offer'));
    }


    public function accept(Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);


        try {
            $this->offerService->accept($offer, auth()->id());


            return redirect()
                ->route('admin.offers.show', $offer)
                ->with('success', 'Penawaran diterima. Unit telah di-reserve dan sales order dibuat.');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('admin.offers.show', $offer)
                ->with('error', $e->getMessage());
        }
    }


    public function reject(Request $request, Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);


        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);


        try {
            $this->offerService->reject($offer, auth()->id(), $request->reason);


            return redirect()
                ->route('admin.offers.show', $offer)
                ->with('success', 'Penawaran ditolak.');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('admin.offers.show', $offer)
                ->with('error', $e->getMessage());
        }
    }
}



