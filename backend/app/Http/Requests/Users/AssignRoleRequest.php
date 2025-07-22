<?php
namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class AssignRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ];
    }

    public function messages(): array
    {
        return [
            'roles.required' => 'Vui lòng chọn ít nhất một vai trò.',
            'roles.*.exists' => 'Vai trò không hợp lệ.',
        ];
    }
}
