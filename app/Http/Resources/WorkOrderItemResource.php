<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class WorkOrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'work_order_id' => $this->work_order_id,
            'type' => $this->type,
            'description' => $this->description,
            'spare_part_id' => $this->spare_part_id,
            'qty' => $this->qty,
            'unit_price_idr' => $this->unit_price_idr,
            'subtotal_idr' => $this->subtotal_idr,
            'spare_part' => new SparePartResource($this->whenLoaded('sparePart')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}



