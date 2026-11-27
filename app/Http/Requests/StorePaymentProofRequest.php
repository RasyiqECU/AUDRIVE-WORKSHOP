<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\PaymentSettings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Dipakai web dan API: hanya pemilik pesanan, gambar maks. 2 MB. */
class StorePaymentProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        $customer = $this->user()->customer;

        return $customer !== null && $this->route('order')->customer_id === $customer->id;
    }

    public function rules(): array
    {
        return [
            'proof' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sender_name' => ['required', 'string', 'max:100'],
            'destination_bank' => ['required', Rule::in(array_column(PaymentSettings::accounts(), 'bank'))],
            'amount_idr' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'proof.required' => 'Bukti transfer wajib diunggah.',
            'proof.uploaded' => 'Gambar gagal diunggah. Ukuran maksimal 2 MB.',
            'proof.image' => 'Bukti harus berupa gambar.',
            'proof.mimes' => 'Format bukti harus JPG, PNG, atau WEBP.',
            'proof.max' => 'Ukuran bukti maksimal 2 MB.',
        ];
    }

    /** @return array{sender_name: string, destination_bank: string, amount_idr: int} */
    public function transferData(): array
    {
        return [
            'sender_name' => $this->validated('sender_name'),
            'destination_bank' => $this->validated('destination_bank'),
            'amount_idr' => (int) $this->validated('amount_idr'),
        ];
    }
}
