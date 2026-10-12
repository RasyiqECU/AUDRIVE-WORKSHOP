<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Requests\Api\V1\CheckoutRequest;
use App\Http\Requests\Api\V1\StoreCartItemRequest;
use App\Http\Requests\Api\V1\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\SalesOrderResource;
use App\Models\CartItem;
use App\Models\SparePart;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CartController extends ApiController
{
    protected CartService $cartService;
    protected CheckoutService $checkoutService;


    public function __construct(CartService $cartService, CheckoutService $checkoutService)
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
    }


    /**
     * Get cart
     */
    public function index(Request $request): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($request->user()?->customer);
        $cart->load('items.sparePart');


        return $this->successResponse(
            new CartResource($cart),
            'Cart retrieved successfully'
        );
    }


    /**
     * Add item to cart
     */
    public function storeItem(StoreCartItemRequest $request): JsonResponse
    {
        $part = SparePart::findOrFail($request->spare_part_id);


        if ($part->status !== 'active') {
            return $this->errorResponse('Part is not available');
        }


        $cart = $this->cartService->getOrCreateCart($request->user()?->customer);


        try {
            $item = $this->cartService->addItem($cart, $part, $request->qty);
            $cart->load('items.sparePart');


            return $this->successResponse(
                new CartResource($cart),
                'Item added to cart successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    /**
     * Update cart item
     */
    public function updateItem(UpdateCartItemRequest $request, int $itemId): JsonResponse
    {
        $item = CartItem::findOrFail($itemId);


        try {
            $this->cartService->updateItemQty($item, $request->qty);
            $item->cart->load('items.sparePart');


            return $this->successResponse(
                new CartResource($item->cart),
                'Cart item updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    /**
     * Remove item from cart
     */
    public function destroyItem(Request $request, int $itemId): JsonResponse
    {
        $item = CartItem::findOrFail($itemId);
        $cart = $item->cart;


        $this->cartService->removeItem($item);
        $cart->load('items.sparePart');


        return $this->successResponse(
            new CartResource($cart),
            'Item removed from cart successfully'
        );
    }


    /**
     * Checkout
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($request->user()?->customer);
        $cart->load('items.sparePart');


        if ($cart->items->isEmpty()) {
            return $this->errorResponse('Cart is empty');
        }


        try {
            $salesOrder = $this->checkoutService->processCheckout(
                $cart,
                $request->user()?->customer,
                $request->shipping_method,
                $request->shipping_address
            );


            return $this->successResponse(
                new SalesOrderResource($salesOrder->load('items')),
                'Checkout successful',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}



