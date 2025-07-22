<?php

namespace App\Http\Requests\RolePermissions;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:6|unique:permissions,name',
            'description' => 'max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute không được để trống.',
            'name.min' => ':attribute tối thiểu :min ký tự.',
            'name.unique' => ':attribute đã tồn tại.',
            'email.description' => ':attribute không được vượt quá :max ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên quyền',
            'description' => 'Mô tả'
        ];
    }
}
