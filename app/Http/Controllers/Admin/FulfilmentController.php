<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Services\Notification\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Antrean gudang: siapkan lalu serahkan/kirim pesanan spare part yang sudah lunas. */
class FulfilmentController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function index(): View
    {
        return view('admin.fulfilments.index', [
            'orders' => $this->queue()->with(['customer.user', 'items'])->oldest()->get(),
        ]);
    }

    public function prepare(SalesOrder $order): RedirectResponse
    {
        if (! $this->queue()->whereKey($order->id)->whereNull('prepared_at')->update(['prepared_at' => now()])) {
            return back()->with('error', 'Pesanan ini tidak menunggu disiapkan.');
        }

        $this->notify($order->refresh(), 'Barang sudah siap '.($order->fulfillment_method === 'delivery' ? 'dikirim.' : 'diambil di bengkel.'));

        return back()->with('success', "Pesanan #{$order->code} siap.");
    }

    public function handOver(SalesOrder $order): RedirectResponse
    {
        if (! $this->queue()->whereKey($order->id)->whereNotNull('prepared_at')->update(['handed_over_at' => now()])) {
            return back()->with('error', 'Siapkan barang terlebih dahulu.');
        }

        $this->notify($order->refresh(), $order->fulfillment_method === 'delivery' ? 'Pesanan sudah dikirim.' : 'Pesanan sudah diambil.');

        return back()->with('success', "Pesanan #{$order->code} diserahkan.");
    }

    private function queue()
    {
        return SalesOrder::where('type', SalesOrderType::SparePart)
            ->where('status', SalesOrderStatus::Paid)
            ->whereNull('handed_over_at');
    }

    private function notify(SalesOrder $order, string $message): void
    {
        if ($user = $order->customer?->user) {
            $this->notificationService->send($user, 'order_fulfilment', [
                'title' => "Pesanan #{$order->code}",
                'message' => $message,
                'action_url' => route('parts.orders.show', $order->id),
            ]);
        }
    }
}
