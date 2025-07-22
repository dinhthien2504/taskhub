<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute không được để trống.',
            'name.min' => ':attribute tối thiểu :min ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên'
        ];
    }
}
