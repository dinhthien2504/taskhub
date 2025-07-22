<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class AssignUsersToProjectRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'user_ids.required' => 'Danh sách người dùng là bắt buộc.',
            'user_ids.array' => 'Danh sách người dùng phải là một mảng.',
            'user_ids.*.exists' => 'Người dùng không tồn tại.',
        ];
    }
}
