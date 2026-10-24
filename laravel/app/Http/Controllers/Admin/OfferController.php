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
        protected OfferService $offerService,
        protected \App\Services\Notification\NotificationService $notificationService
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
        $offer->load(['carUnit.photos', 'customer.user', 'reviewer']);

        $salesOrder = \App\Models\SalesOrder::where('type', \App\Enums\SalesOrderType::CarSales)
            ->where('notes', 'like', "%offer #{$offer->id}%")
            ->first();

        return view('admin.offers.show', compact('offer', 'salesOrder'));
    }

    public function accept(Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);

        try {
            $this->offerService->accept($offer, auth()->id());

            // Notifikasi ke Customer bahwa penawaran disetujui
            if ($offer->customer?->user) {
                $carName = $offer->carUnit ? ($offer->carUnit->brand . ' ' . $offer->carUnit->model) : 'Unit Mobil';
                $this->notificationService->send($offer->customer->user, 'offer_accepted', [
                    'title'      => "Penawaran Disetujui: {$carName}",
                    'message'    => "Selamat! Penawaran harga Anda sebesar Rp " . number_format($offer->amount_idr, 0, ',', '.') . " untuk {$carName} telah DITERIMA oleh Sales AUDrive. Unit telah di-reserve untuk Anda.",
                    'car_name'   => $carName,
                    'amount'     => $offer->amount_idr,
                    'action_url' => route('customer.dashboard'),
                ]);
            }

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

            // Notifikasi ke Customer bahwa penawaran ditolak
            if ($offer->customer?->user) {
                $carName = $offer->carUnit ? ($offer->carUnit->brand . ' ' . $offer->carUnit->model) : 'Unit Mobil';
                $this->notificationService->send($offer->customer->user, 'offer_rejected', [
                    'title'      => "Penawaran Belum Disetujui: {$carName}",
                    'message'    => "Penawaran harga Anda untuk {$carName} belum dapat disetujui oleh Sales." . ($request->reason ? " Alasan: {$request->reason}" : ''),
                    'car_name'   => $carName,
                    'action_url' => $offer->carUnit ? route('marketplace.show', $offer->carUnit->slug) : null,
                ]);
            }

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



