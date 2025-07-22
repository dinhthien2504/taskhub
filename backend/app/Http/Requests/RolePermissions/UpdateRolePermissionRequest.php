<?php

namespace App\Http\Requests\RolePermissions;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRolePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'permission_ids' => ['required', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'permission_ids' => 'Danh sách quyền',
            'permission_ids.*' => 'Quyền cụ thể',
        ];
    }

    public function messages(): array
    {
        return [
            'permission_ids.required' => 'Vui lòng chọn ít nhất một quyền.',
            'permission_ids.array' => 'Dữ liệu quyền phải là dạng danh sách.',
            'permission_ids.*.exists' => 'Một hoặc nhiều quyền không hợp lệ.',
        ];
    }
}
