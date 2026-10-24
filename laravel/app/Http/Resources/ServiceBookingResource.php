<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ServiceBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'customer_vehicle_id' => $this->customer_vehicle_id,
            'service_type' => $this->service_type,
            'preferred_date' => $this->preferred_date,
            'preferred_time' => $this->preferred_time,
            'status' => $this->status,
            'estimated_cost_idr' => $this->estimated_cost_idr,
            'notes' => $this->notes,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'vehicle' => new CustomerVehicleResource($this->whenLoaded('vehicle')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}



