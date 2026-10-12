<?php


declare(strict_types=1);


namespace App\Http\Requests\Admin;


use Illuminate\Foundation\Http\FormRequest;


class StoreSparePartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\SparePart::class);
    }


    public function rules(): array
    {
        return [
            'sku' => 'required|string|max:50|unique:spare_parts,sku',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:spare_part_categories,id',
            'brand' => 'nullable|string|max:100',
            'price_idr' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];
    }


    public function messages(): array
    {
        return [
            'sku.required' => 'SKU wajib diisi.',
            'sku.unique' => 'SKU sudah digunakan.',
            'name.required' => 'Nama part wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'price_idr.required' => 'Harga wajib diisi.',
            'price_idr.min' => 'Harga tidak boleh negatif.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.min' => 'Stok tidak boleh negatif.',
            'low_stock_threshold.required' => 'Threshold stok rendah wajib diisi.',
        ];
    }
}



