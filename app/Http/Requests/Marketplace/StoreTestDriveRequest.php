<?php


declare(strict_types=1);


namespace App\Http\Requests\Marketplace;


use App\Enums\CarUnitStatus;
use App\Models\CarUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreTestDriveRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Customer harus login dan memiliki customer profile
        return $this->user() && $this->user()->customer;
    }


    public function rules(): array
    {
        return [
            'scheduled_at' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:500'],
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
                $validator->errors()->add('car_unit', 'Unit tidak tersedia untuk test drive.');
            }
        });
    }


    public function messages(): array
    {
        return [
            'scheduled_at.required' => 'Jadwal test drive wajib diisi.',
            'scheduled_at.date' => 'Jadwal test drive harus berupa tanggal yang valid.',
            'scheduled_at.after' => 'Jadwal test drive tidak boleh di masa lalu.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}



