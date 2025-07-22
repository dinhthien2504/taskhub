<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:6|unique:projects,name',
            'owner_id' => 'required|integer',
            'description' => 'max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute không được để trống.',
            'name.min' => ':attribute tối thiểu :min ký tự.',
            'name.unique' => ':attribute đã tồn tại.',

            'owner_id.required' => ':attribute không được để trống.',

            'description.max' => ':attribute không được vượt quá :max ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên dự án',
            'description' => 'Mô tả',
            'owner_id' => 'Người sở hữu'
        ];
    }
}
