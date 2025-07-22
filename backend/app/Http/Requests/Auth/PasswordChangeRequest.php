<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PasswordChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                'confirmed',
            ],
            'password_confirmation' => [
                'required'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => ':attribute không được để trống.',
            'current_password.string' => ':attribute phải là chuỗi ký tự.',

            'password.required' => ':attribute không được để trống.',
            'password.string' => ':attribute phải là chuỗi ký tự.',
            'password.min' => ':attribute phải có ít nhất :min ký tự.',
            'password.regex' => ':attribute phải chứa ít nhất 1 ký tự đặt biệt, 1 chữ hoa và 1 chữ số.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',

            'password_confirmation.required' => ':attribute không được để trống.',
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => 'Mật khẩu cũ',
            'password' => 'Mật khẩu mới',
            'password_confirmation' => 'Mật khẩu xác nhận',
        ];
    }
}
