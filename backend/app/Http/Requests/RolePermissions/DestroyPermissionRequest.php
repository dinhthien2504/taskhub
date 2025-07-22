<?php
namespace App\Http\Requests\RolePermissions;

use Illuminate\Foundation\Http\FormRequest;

class DestroyPermissionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:permissions,id',
        ];
    }

    public function messages()
    {
        return [
            'ids.required' => 'Vui lòng chọn quyền cần xoá.',
            'ids.array' => 'Danh sách ID không hợp lệ.',
            'ids.*.exists' => 'Quyền không tồn tại trong hệ thống.',
        ];
    }
}
