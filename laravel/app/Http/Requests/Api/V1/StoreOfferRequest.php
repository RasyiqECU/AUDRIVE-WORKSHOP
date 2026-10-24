<?php


namespace App\Http\Requests\Api\V1;


use Illuminate\Foundation\Http\FormRequest;


class StoreOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'car_unit_id' => ['required', 'exists:car_units,id'],
            'amount_idr' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}



