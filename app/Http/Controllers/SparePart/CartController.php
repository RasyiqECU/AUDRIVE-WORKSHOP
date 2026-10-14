<?php


declare(strict_types=1);


namespace App\Http\Controllers\SparePart;


use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\SparePart;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}


    public function index(Request $request): View
    {
        $cart = $this->cartService->getOrCreateCart($request->user()?->customer);
        $cart->load('items.sparePart');


        return view('spare-parts.cart', compact('cart'));
    }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'spare_part_id' => 'required|exists:spare_parts,id',
            'qty' => 'nullable|integer|min:1',
        ]);


        $part = SparePart::findOrFail($validated['spare_part_id']);


        $statusValue = $part->status instanceof \App\Enums\SparePartStatus ? $part->status->value : (string) $part->status;
        if ($statusValue !== 'active') {
            return back()->with('error', 'Part tidak tersedia.');
        }


        $cart = $this->cartService->getOrCreateCart($request->user()?->customer);


        try {
            $qty = (int) ($validated['qty'] ?? $request->input('qty', 1));
            $this->cartService->addItem($cart, $part, $qty);
            return back()->with('success', 'Berhasil ditambahkan ke keranjang.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:1',
        ]);


        try {
            $this->cartService->updateItemQty($item, (int) $validated['qty']);
            return back()->with('success', 'Qty berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function destroy(CartItem $item): RedirectResponse
    {
        $this->cartService->removeItem($item);
        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}



