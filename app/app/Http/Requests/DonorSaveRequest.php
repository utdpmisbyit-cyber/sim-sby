<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonorSaveRequest extends FormRequest
{
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
            'tanggal_lahir' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(17)->format('Y-m-d'),
                'after_or_equal:' . now()->subYears(60)->format('Y-m-d'),
            ],
            'skrining' => 'nullable|string|max:255',
            'no_fpup'  => 'nullable|string|max:255',
            'fpup_id'  => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'tanggal_lahir.before_or_equal' => 'Usia minimal adalah 17 tahun.',
            'tanggal_lahir.after_or_equal' => 'Usia maksimal adalah 60 tahun.',
        ];
    }
}
