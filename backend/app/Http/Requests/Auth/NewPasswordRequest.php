<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class NewPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => [
                'required'
            ],
            'email' => [
                'required',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
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
            'token.required' => 'Vui lòng nhập mã token.',

            'email.required' => ':attribute không được để trống.',
            'email.regex' => ':attribute không đúng định dạng.',

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
            'password' => 'Mật khẩu mới',
            'password_confirmation' => 'Mật khẩu xác nhận',
        ];
    }
}
