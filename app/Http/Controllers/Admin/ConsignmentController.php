<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarUnit;
use App\Services\ConsignmentService;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConsignmentController extends Controller
{
    public function __construct(protected ConsignmentService $consignments) {}

    public function approve(Request $request, CarUnit $car): RedirectResponse
    {
        $data = $request->validate([
            'min_price_idr' => 'required|integer|min:1000000',
            'price_sell_idr' => 'required|integer|gte:min_price_idr',
            'commission_idr' => 'required|integer|min:0|lt:min_price_idr',
            'seller_warranty_verified' => 'nullable|boolean',
        ]);
        $data['seller_warranty_verified'] = $request->boolean('seller_warranty_verified');

        return $this->attempt(fn () => $this->consignments->approve($car, $data), 'Unit titip jual sudah tayang.');
    }

    public function reject(Request $request, CarUnit $car): RedirectResponse
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);

        return $this->attempt(fn () => $this->consignments->reject($car, $data['reason']), 'Pengajuan ditolak.');
    }

    public function payout(Request $request, CarUnit $car): RedirectResponse
    {
        return $this->attempt(fn () => $this->consignments->paySeller($car, $request->user()), 'Pembayaran ke penjual dicatat.');
    }

    private function attempt(Closure $action, string $success): RedirectResponse
    {
        try {
            $action();
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $success);
    }
}
