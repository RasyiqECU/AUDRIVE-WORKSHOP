<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'total' => $this->whenLoaded('items', fn() => $this->items->sum(fn($item) => $item->qty * $item->price_idr)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}



