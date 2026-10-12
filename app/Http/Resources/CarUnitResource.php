<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CarUnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'slug' => $this->slug,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'transmission' => $this->transmission,
            'color' => $this->color,
            'mileage_km' => $this->mileage_km,
            'price_sell_idr' => $this->price_sell_idr,
            'status' => $this->status,
            'description' => $this->description,
            'stock_status' => $this->stock_status,
            'warranty_status' => $this->warranty_status,
            'stnk_status' => $this->stnk_status,
            'bpkb_status' => $this->bpkb_status,
            'location' => $this->location,
            'featured_at' => $this->featured_at,
            'fuel_type' => $this->fuel_type,
            'engine_cc' => $this->engine_cc,
            'exterior_color' => $this->exterior_color,
            'interior_color' => $this->interior_color,
            'primary_photo' => $this->whenLoaded('primaryPhoto', fn() => $this->primaryPhoto?->path),
            'photos' => PhotoResource::collection($this->whenLoaded('photos')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}



