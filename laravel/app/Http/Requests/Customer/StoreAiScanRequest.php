<?php


declare(strict_types=1);


namespace App\Http\Requests\Customer;


use Illuminate\Foundation\Http\FormRequest;


class StoreAiScanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'audio' => ['required', 'file', 'mimes:wav,mp3,m4a', 'max:20480'], // 20MB
            'customer_vehicle_id' => ['nullable', 'exists:customer_vehicles,id'],
        ];
    }


    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'audio.required' => 'File audio wajib diupload.',
            'audio.file' => 'File audio tidak valid.',
            'audio.mimes' => 'Format audio harus WAV, MP3, atau M4A.',
            'audio.max' => 'Ukuran file audio maksimal 20MB.',
            'customer_vehicle_id.exists' => 'Kendaraan tidak ditemukan.',
        ];
    }
}



