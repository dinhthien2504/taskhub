<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3',
            'phone' => 'nullable|max:15',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute không được để trống.',
            'phone.max' => ':attribute không được vượt quá :max ký tự.',
            'avatar.image' => 'Ảnh đại diện phải là file hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng: jpeg, png, jpg, gif, webp.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 1MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên',
            'phone' => 'Số điện thoại',
        ];
    }
}
