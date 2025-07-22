<?php
namespace App\Http\Requests\RolePermissions;

use Illuminate\Foundation\Http\FormRequest;

class DestroyRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:roles,id',
        ];
    }

    public function messages()
    {
        return [
            'ids.required' => 'Vui lòng chọn vai trò cần xoá.',
            'ids.array' => 'Danh sách ID không hợp lệ.',
            'ids.*.exists' => 'Vai trò không tồn tại trong hệ thống.',
        ];
    }
}
