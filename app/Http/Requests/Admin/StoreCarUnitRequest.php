<?php


declare(strict_types=1);


namespace App\Http\Requests\Admin;


use Illuminate\Foundation\Http\FormRequest;


class StoreCarUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\CarUnit::class);
    }


    protected function prepareForValidation(): void
    {
        $intFields = ['mileage_km', 'price_buy_idr', 'price_sell_idr', 'year', 'engine_cc'];
        $merge = [];

        foreach ($intFields as $field) {
            if ($this->has($field) && $this->input($field) !== '' && $this->input($field) !== null) {
                $merge[$field] = (int) $this->input($field);
            }
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }


    public function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'year' => ['required', 'integer', 'min:1950', 'max:' . (date('Y') + 1)],
            'transmission' => ['required', 'in:manual,automatic,cvt'],
            'color' => ['nullable', 'string', 'max:30'],
            'mileage_km' => ['required', 'integer', 'min:0'],
            'price_buy_idr' => ['required', 'integer', 'min:0'],
            'price_sell_idr' => ['required', 'integer', 'min:0', 'gte:price_buy_idr'],
            'description' => ['nullable', 'string', 'max:2000'],
            'stock_status' => ['required', 'in:available,reserved,sold'],
            'warranty_status' => ['nullable', 'in:active,expired'],
            'stnk_status' => ['nullable', 'in:valid,expired'],
            'bpkb_status' => ['nullable', 'in:available,missing'],
            'location' => ['nullable', 'string', 'max:100'],
            'fuel_type' => ['nullable', 'in:bensin,diesel,hybrid,electric'],
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
            'price_sell_idr.gte' => 'Harga jual harus lebih besar atau sama dengan harga beli.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }
}



