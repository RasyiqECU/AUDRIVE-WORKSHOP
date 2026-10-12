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
        $request->validate([
            'spare_part_id' => 'required|exists:spare_parts,id',
            'qty' => 'integer|min:1',
        ]);


        $part = SparePart::findOrFail($request->spare_part_id);


        if ($part->status->value !== 'active') {
            return back()->with('error', 'Part tidak tersedia.');
        }


        $cart = $this->cartService->getOrCreateCart($request->user()?->customer);


        try {
            $this->cartService->addItem($cart, $part, $request->input('qty', 1));
            return back()->with('success', 'Berhasil ditambahkan ke keranjang.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);


        try {
            $this->cartService->updateItemQty($item, $request->qty);
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



