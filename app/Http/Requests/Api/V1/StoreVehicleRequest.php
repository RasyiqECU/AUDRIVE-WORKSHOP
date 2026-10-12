<?php


namespace App\Http\Requests\Api\V1;


use Illuminate\Foundation\Http\FormRequest;


class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'plate_number' => ['nullable', 'string', 'max:15'],
            'vin' => ['nullable', 'string', 'max:17', 'unique:customer_vehicles,vin'],
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'year' => ['required', 'integer', 'min:1950', 'max:' . (date('Y') + 1)],
            'color' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}



