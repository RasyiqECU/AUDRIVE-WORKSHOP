<?php


declare(strict_types=1);


namespace App\Http\Requests\Admin;


use Illuminate\Foundation\Http\FormRequest;


class ReviewAiScanRequest extends FormRequest
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
            'technician_review' => ['required', 'string', 'max:1000'],
            'predicted_class' => ['nullable', 'string', 'max:100'],
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
            'technician_review.required' => 'Review teknisi wajib diisi.',
            'technician_review.max' => 'Review teknisi maksimal 1000 karakter.',
            'predicted_class.max' => 'Kelas prediksi maksimal 100 karakter.',
        ];
    }
}



