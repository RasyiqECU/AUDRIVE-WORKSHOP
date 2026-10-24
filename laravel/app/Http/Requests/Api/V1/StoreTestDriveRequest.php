<?php


namespace App\Http\Requests\Api\V1;


use Illuminate\Foundation\Http\FormRequest;


class StoreTestDriveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'car_unit_id' => ['required', 'exists:car_units,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}



