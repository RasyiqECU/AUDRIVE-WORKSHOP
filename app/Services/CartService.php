<?php


declare(strict_types=1);


namespace App\Services;


use App\Models\Cart;
use App\Models\CartItem;
use App\Models\SparePart;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;


class CartService
{
    public function getOrCreateCart(?Customer $customer): Cart
    {
        if ($customer) {
            return Cart::firstOrCreate(
                ['customer_id' => $customer->id],
                ['session_id' => null]
            );
        }


        // Guest cart
        $sessionId = session()->getId();
        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['customer_id' => null]
        );
    }


    public function addItem(Cart $cart, SparePart $part, int $qty = 1): CartItem
    {
        return DB::transaction(function () use ($cart, $part, $qty) {
            // Check stock availability
            if ($part->stock < $qty) {
                throw new \Exception("Stok tidak cukup untuk {$part->name}. Tersedia: {$part->stock}");
            }


            // Check if item already in cart
            $existingItem = $cart->items()->where('spare_part_id', $part->id)->first();


            if ($existingItem) {
                // Update quantity
                $newQty = $existingItem->qty + $qty;
                if ($part->stock < $newQty) {
                    throw new \Exception("Stok tidak cukup untuk {$part->name}. Tersedia: {$part->stock}");
                }
                $existingItem->update(['qty' => $newQty]);
                return $existingItem;
            }


            // Create new item
            return $cart->items()->create([
                'spare_part_id' => $part->id,
                'qty' => $qty,
                'price_idr' => $part->price_idr,
            ]);
        });
    }


    public function updateItemQty(CartItem $item, int $qty): CartItem
    {
        return DB::transaction(function () use ($item, $qty) {
            $part = SparePart::findOrFail($item->spare_part_id);


            if ($part->stock < $qty) {
                throw new \Exception("Stok tidak cukup untuk {$part->name}. Tersedia: {$part->stock}");
            }


            $item->update(['qty' => $qty]);
            return $item;
        });
    }


    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }


    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
    }


    public function calculateTotal(Cart $cart): int
    {
        return $cart->items->sum(function ($item) {
            return $item->qty * $item->price_idr;
        });
    }


    public function mergeGuestCartToCustomer(Cart $guestCart, Customer $customer): void
    {
        $customerCart = $this->getOrCreateCart($customer);


        foreach ($guestCart->items as $guestItem) {
            try {
                $this->addItem($customerCart, $guestItem->sparePart, $guestItem->qty);
            } catch (\Exception $e) {
                // Skip items that can't be merged
                continue;
            }
        }


        // Clear guest cart
        $this->clearCart($guestCart);
        $guestCart->delete();
    }
}



