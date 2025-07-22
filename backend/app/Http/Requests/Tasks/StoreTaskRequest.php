<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'task_status_id' => 'required|exists:task_statuses,id',
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function messages()
    {
        return [
            'task_status_id.required' => 'Vui lòng chọn trạng thái.',
            'task_status_id.exists' => 'Trạng thái không hợp lệ.',
            'title.required' => 'Tên nhiệm vụ không được để trống.',
            'title.min' => 'Tên nhiệm vụ không được quá ngắn, tối thiểu 3 ký tự.',
            'title.max' => 'Tên nhiệm vụ không được dài quá 255 ký tự.',
            'assigned_to.exists' => 'Người được giao không tồn tại.',
            'due_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ];
    }
}
