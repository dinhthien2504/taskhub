<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:6',
            'email' => [
                'required',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            ],
            'phone' => 'nullable|max:15'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute không được để trống.',
            'name.min' => ':attribute tối thiểu :min ký tự.',
            'email.required' => ':attribute không được để trống.',
            'email.regex' => ':attribute không đúng định dạng.',
            'email.unique' => ':attribute đã tồn tại.',
            'password.required' => ':attribute không được để trống.',
            'password.min' => ':attribute phải có ít nhất :min ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.regex' => ':attribute phải chứa ít nhất 1 ký tự đặt biệt, 1 chữ hoa và 1 chữ số.',
            'phone.max' => ':attribute không được vượt quá :max ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'phone' => 'Số điện thoại',
        ];
    }
}
