<?php

declare(strict_types=1);

namespace App\Http\Controllers\SparePart;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $customer = $request->user()?->customer;

        $orders = SalesOrder::where('customer_id', $customer?->id)
            ->with(['items.sparePart'])
            ->latest()
            ->paginate(10);

        return view('spare-parts.orders.index', compact('orders'));
    }

    public function show(Request $request, SalesOrder $order): View
    {
        $customer = $request->user()?->customer;

        if ($customer && $order->customer_id !== $customer->id) {
            abort(403);
        }

        $order->load(['items.sparePart', 'customer']);

        return view('spare-parts.orders.show', compact('order'));
    }

    public function invoice(Request $request, SalesOrder $order): View
    {
        $customer = $request->user()?->customer;

        if ($customer && $order->customer_id !== $customer->id) {
            abort(403);
        }

        $order->load(['items.sparePart', 'customer']);

        return view('spare-parts.orders.show', compact('order'));
    }
}
