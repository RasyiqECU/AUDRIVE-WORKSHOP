<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class WorkOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'service_booking_id' => $this->service_booking_id,
            'customer_vehicle_id' => $this->customer_vehicle_id,
            'status' => $this->status,
            'assigned_mechanic_id' => $this->assigned_mechanic_id,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'notes' => $this->notes,
            'total_jasa_idr' => $this->total_jasa_idr,
            'total_part_idr' => $this->total_part_idr,
            'total_idr' => $this->total_idr,
            'vehicle' => new CustomerVehicleResource($this->whenLoaded('vehicle')),
            'mechanic' => new UserResource($this->whenLoaded('mechanic')),
            'items' => WorkOrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}



