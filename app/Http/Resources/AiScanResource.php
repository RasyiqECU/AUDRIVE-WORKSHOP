<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class AiScanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'customer_vehicle_id' => $this->customer_vehicle_id,
            'uploaded_by' => $this->uploaded_by,
            'audio_path' => $this->audio_path,
            'status' => $this->status,
            'predicted_class' => $this->predicted_class,
            'confidence' => $this->confidence,
            'recommendations' => $this->recommendations,
            'spectrogram_path' => $this->spectrogram_path,
            'technician_review' => $this->technician_review,
            'reviewed_by' => $this->reviewed_by,
            'reviewed_at' => $this->reviewed_at,
            'vehicle' => new CustomerVehicleResource($this->whenLoaded('vehicle')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}



