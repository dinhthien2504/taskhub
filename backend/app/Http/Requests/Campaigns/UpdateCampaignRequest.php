<?php

namespace App\Http\Requests\Campaigns;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaignRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:pending,scheduled',
            'scheduled_at' => 'nullable|date',
            'template_id' => 'required|integer|exists:email_templates,id'
        ];

    }

    public function messages()
    {
        return [
            'name.required' => 'Tên chiến dịch là bắt buộc.',
            'name.string' => 'Tên chiến dịch phải là chuỗi ký tự.',
            'name.max' => 'Tên chiến dịch không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là chuỗi ký tự.',
            'start_date.required' => 'Ngày bắt đầu không được để trống.',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ.',
            'end_date.required' => 'Ngày kết thúc không không được để trống.',
            'end_date.date' => 'Ngày kết thúc không hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.string' => 'Trạng thái phải là chuỗi ký tự.',
            'scheduled_at.date' => 'Thời gian lên lịch không hợp lệ.',
            'template_id.required' => 'Vui lòng chọn mẫu chiến dịch.',
            'template_id.integer' => 'Mã mẫu chiến dịch phải là số.',
            'template_id.exists' => 'Mã mẫu chiến dịch không hợp lệ.'
        ];
    }
}
