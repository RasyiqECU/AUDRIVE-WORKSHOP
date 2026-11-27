<?php

declare(strict_types=1);

namespace App\Http\Controllers\SparePart;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CheckoutService $checkoutService
    ) {}

    public function index(Request $request): View
    {
        $cart = $this->cartService->getOrCreateCart($request->user()?->customer);
        $cart->load('items.sparePart');

        if ($cart->items->isEmpty()) {
            return redirect()->route('parts.index')->with('error', 'Keranjang Anda kosong.');
        }

        return view('spare-parts.checkout', compact('cart'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Tanpa profil customer, pesanan yang dibuat tidak bisa dibuka pemiliknya
        if (! $request->user()->customer && $request->user()->role !== 'super-admin') {
            return back()->with('error', 'Checkout online hanya untuk akun customer. Staf gunakan POS kasir.');
        }

        $request->validate([
            'shipping_method' => 'required|in:pickup,delivery',
            'shipping_address' => 'nullable|string|max:500',
        ]);

        $cart = $this->cartService->getOrCreateCart($request->user()?->customer);

        if ($cart->items->isEmpty()) {
            return redirect()->route('parts.index')->with('error', 'Keranjang Anda kosong.');
        }

        try {
            $salesOrder = $this->checkoutService->processCheckout(
                $cart,
                $request->user()?->customer,
                $request->shipping_method,
                $request->shipping_address
            );

            return redirect()
                ->route('parts.orders.show', $salesOrder)
                ->with('success', 'Checkout berhasil! Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function payment(Request $request, int $orderId): View
    {
        $salesOrder = SalesOrder::findOrFail($orderId);

        // Verify ownership
        $user = $request->user();
        abort_unless(
            $user->role === 'super-admin' || ($user->customer && $salesOrder->customer_id === $user->customer->id),
            403
        );

        return view('spare-parts.payment', compact('salesOrder'));
    }
}
