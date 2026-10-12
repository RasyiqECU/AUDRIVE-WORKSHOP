<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class SalesOrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sales_order_id' => $this->sales_order_id,
            'item_type' => $this->item_type,
            'car_unit_id' => $this->car_unit_id,
            'spare_part_id' => $this->spare_part_id,
            'description' => $this->description,
            'qty' => $this->qty,
            'unit_price_idr' => $this->unit_price_idr,
            'subtotal_idr' => $this->subtotal_idr,
            'car_unit' => new CarUnitResource($this->whenLoaded('carUnit')),
            'spare_part' => new SparePartResource($this->whenLoaded('sparePart')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}



