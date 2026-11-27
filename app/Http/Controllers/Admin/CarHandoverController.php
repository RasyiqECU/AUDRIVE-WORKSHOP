<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Sales menyerahkan mobil yang sudah lunas: diambil sendiri atau diantar towing. */
class CarHandoverController extends Controller
{
    public function index(): View
    {
        return view('admin.car-handovers.index', [
            'orders' => $this->queue()->with(['customer.user', 'items'])->oldest()->get(),
        ]);
    }

    public function store(SalesOrder $order): RedirectResponse
    {
        if (! $this->queue()->whereKey($order->id)->update(['handed_over_at' => now()])) {
            return back()->with('error', 'Pesanan ini tidak menunggu serah terima.');
        }

        $order->refresh()->assignSales(auth()->user());

        return back()->with('success', "Serah terima #{$order->code} dicatat.");
    }

    private function queue()
    {
        return SalesOrder::where('type', SalesOrderType::CarSales)
            ->where('status', SalesOrderStatus::Paid)
            ->whereNull('handed_over_at');
    }
}
