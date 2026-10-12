<?php


declare(strict_types=1);


namespace App\Http\Requests\Workshop;


use Illuminate\Foundation\Http\FormRequest;


class StoreServiceBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'customer_vehicle_id' => 'required|exists:customer_vehicles,id',
            'service_type' => 'required|in:regular_service,heavy_repair,body_repair,ac_service,inspection',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ];
    }


    public function messages(): array
    {
        return [
            'customer_vehicle_id.required' => 'Silakan pilih kendaraan.',
            'customer_vehicle_id.exists' => 'Kendaraan tidak ditemukan.',
            'service_type.required' => 'Silakan pilih jenis servis.',
            'service_type.in' => 'Jenis servis tidak valid.',
            'preferred_date.required' => 'Silakan pilih tanggal.',
            'preferred_date.date' => 'Format tanggal tidak valid.',
            'preferred_date.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
            'preferred_time.required' => 'Silakan pilih waktu.',
            'preferred_time.date_format' => 'Format waktu tidak valid. Gunakan format HH:MM.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}



