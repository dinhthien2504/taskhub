<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
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
            'phone' => 'nullable|string|max:15',
            'avatar' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute không được để trống.',
            'name.min' => ':attribute tối thiểu :min ký tự.',
            'name.max' => ':attribute không được vượt quá :max ký tự.',

            'email.required' => ':attribute không được để trống.',
            'email.regex' => ':attribute không đúng định dạng.',
            'email.unique' => ':attribute đã tồn tại.',

            'password.required' => ':attribute không được để trống.',
            'password.min' => ':attribute phải có ít nhất :min ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.regex' => ':attribute phải chứa ít nhất 1 ký tự đặc biệt, 1 chữ hoa và 1 chữ số.',

            'phone.max' => ':attribute không được vượt quá :max ký tự.',
            'avatar.max' => ':attribute không được vượt quá :max ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'phone' => 'Số điện thoại',
            'avatar' => 'Ảnh đại diện',
        ];
    }
}
