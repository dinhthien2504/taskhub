<?php

namespace App\Http\Requests\Campaigns;

use Illuminate\Foundation\Http\FormRequest;

class AssignUsersToCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'user_ids.required' => 'Danh sách người dùng là bắt buộc.',
            'user_ids.array' => 'Dữ liệu người dùng phải là một mảng.',
            'user_ids.*.exists' => 'Người dùng không tồn tại.',
        ];
    }
}
