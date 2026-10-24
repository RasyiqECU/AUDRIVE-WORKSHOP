<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class SparePartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'slug' => $this->slug,
            'category_id' => $this->category_id,
            'brand' => $this->brand,
            'price_idr' => $this->price_idr,
            'stock' => $this->stock,
            'low_stock_threshold' => $this->low_stock_threshold,
            'status' => $this->status,
            'description' => $this->description,
            'image' => $this->image,
            'weight_kg' => $this->weight_kg,
            'category' => new SparePartCategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}



