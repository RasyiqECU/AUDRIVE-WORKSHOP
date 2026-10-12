<?php


declare(strict_types=1);


namespace App\Http\Requests\Marketplace;


use App\Enums\CarUnitStatus;
use App\Models\CarUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Customer harus login dan memiliki customer profile
        return $this->user() && $this->user()->customer;
    }


    public function rules(): array
    {
        return [
            'amount_idr' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $car = $this->route('car');


            if (!$car instanceof CarUnit) {
                $car = CarUnit::find($this->route('car'));
            }


            if ($car && $car->status !== CarUnitStatus::Available) {
                $validator->errors()->add('car_unit', 'Unit tidak tersedia untuk penawaran.');
            }
        });
    }


    public function messages(): array
    {
        return [
            'amount_idr.required' => 'Jumlah penawaran wajib diisi.',
            'amount_idr.integer' => 'Jumlah penawaran harus berupa angka.',
            'amount_idr.min' => 'Jumlah penawaran tidak boleh negatif.',
            'note.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}



