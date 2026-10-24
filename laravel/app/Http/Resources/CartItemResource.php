<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'spare_part_id' => $this->spare_part_id,
            'qty' => $this->qty,
            'price_idr' => $this->price_idr,
            'subtotal' => $this->qty * $this->price_idr,
            'spare_part' => new SparePartResource($this->whenLoaded('sparePart')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}



