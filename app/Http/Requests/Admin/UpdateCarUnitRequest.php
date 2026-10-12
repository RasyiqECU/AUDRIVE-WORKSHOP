<?php


declare(strict_types=1);


namespace App\Http\Requests\Admin;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateCarUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('car'));
    }


    public function rules(): array
    {
        return [
            'brand' => ['sometimes', 'required', 'string', 'max:50'],
            'model' => ['sometimes', 'required', 'string', 'max:50'],
            'year' => ['sometimes', 'required', 'integer', 'min:1950', 'max:' . (date('Y') + 1)],
            'transmission' => ['sometimes', 'required', Rule::in(['manual', 'automatic', 'cvt'])],
            'color' => ['nullable', 'string', 'max:30'],
            'mileage_km' => ['sometimes', 'required', 'integer', 'min:0'],
            'price_buy_idr' => ['sometimes', 'required', 'integer', 'min:0'],
            'price_sell_idr' => ['sometimes', 'required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'stock_status' => ['sometimes', 'required', Rule::in(['available', 'reserved', 'sold'])],
            'warranty_status' => ['nullable', Rule::in(['active', 'expired'])],
            'stnk_status' => ['nullable', Rule::in(['valid', 'expired'])],
            'bpkb_status' => ['nullable', Rule::in(['available', 'missing'])],
            'location' => ['nullable', 'string', 'max:100'],
            'fuel_type' => ['nullable', Rule::in(['bensin', 'diesel', 'hybrid', 'electric'])],
            'engine_cc' => ['nullable', 'string', 'max:10'],
            'exterior_color' => ['nullable', 'string', 'max:30'],
            'interior_color' => ['nullable', 'string', 'max:30'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB (sesuai php.ini upload_max_filesize)
        ];
    }


    public function messages(): array
    {
        return [
            'brand.required' => 'Merek wajib diisi.',
            'model.required' => 'Model wajib diisi.',
            'year.required' => 'Tahun wajib diisi.',
            'year.min' => 'Tahun minimal 1950.',
            'year.max' => 'Tahun maksimal ' . (date('Y') + 1) . '.',
            'mileage_km.required' => 'Kilometer wajib diisi.',
            'mileage_km.min' => 'Kilometer tidak boleh negatif.',
            'price_buy_idr.required' => 'Harga beli wajib diisi.',
            'price_sell_idr.required' => 'Harga jual wajib diisi.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.max' => 'Ukuran foto maksimal 5MB.',
        ];
    }
}



