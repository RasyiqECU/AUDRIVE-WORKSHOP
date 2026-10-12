<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class OfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'car_unit_id' => $this->car_unit_id,
            'customer_id' => $this->customer_id,
            'amount_idr' => $this->amount_idr,
            'status' => $this->status,
            'note' => $this->note,
            'expires_at' => $this->expires_at,
            'reviewed_by' => $this->reviewed_by,
            'car_unit' => new CarUnitResource($this->whenLoaded('carUnit')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}



