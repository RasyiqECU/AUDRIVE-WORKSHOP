<?php


namespace App\Http\Requests\Api\V1;


use Illuminate\Foundation\Http\FormRequest;


class StoreCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'spare_part_id' => ['required', 'exists:spare_parts,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ];
    }
}



