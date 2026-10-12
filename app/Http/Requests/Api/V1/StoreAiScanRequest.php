<?php


namespace App\Http\Requests\Api\V1;


use Illuminate\Foundation\Http\FormRequest;


class StoreAiScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'audio' => ['required', 'file', 'mimes:wav,mp3,m4a', 'max:20480'], // 20MB
            'customer_vehicle_id' => ['nullable', 'exists:customer_vehicles,id'],
        ];
    }
}



