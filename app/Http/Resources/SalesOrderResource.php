<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class SalesOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'customer_id' => $this->customer_id,
            'type' => $this->type,
            'status' => $this->status,
            'subtotal_idr' => $this->subtotal_idr,
            'discount_idr' => $this->discount_idr,
            'tax_idr' => $this->tax_idr,
            'total_idr' => $this->total_idr,
            'paid_idr' => $this->paid_idr,
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'items' => SalesOrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}



