<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = request()->route()->parameter('user') ?? '';
        return [
            'name' => 'required',
            'role' => 'required',
            'email' => 'required|unique:users,email' . ($id !== '' ? (',' . $id) : ''),
            'password' => 'confirmed' . ($id === '' ? '|required' : '')
        ];
    }
}
