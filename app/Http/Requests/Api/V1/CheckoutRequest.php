<?php


namespace App\Http\Requests\Api\V1;


use Illuminate\Foundation\Http\FormRequest;


class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'shipping_method' => ['required', 'in:pickup,delivery'],
            'shipping_address' => ['nullable', 'string', 'max:500'],
        ];
    }
}



