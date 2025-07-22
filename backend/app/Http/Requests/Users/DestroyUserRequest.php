<?php
namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class DestroyUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'ids.required' => 'Vui lòng chọn người dùng cần xoá.',
            'ids.array' => 'Danh sách ID không hợp lệ.',
            'ids.*.exists' => 'Người dùng không tồn tại trong hệ thống.',
        ];
    }
}
