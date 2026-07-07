<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonorSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal_lahir' => [
                'required',
                'date',
                'before_or_equal:' . now()->format('Y-m-d'), 
            ],
            'skrining' => 'nullable|string|max:255',
            'no_fpup'  => 'nullable|string|max:255',
            'fpup_id'  => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh di masa depan.',
        ];
    }
}