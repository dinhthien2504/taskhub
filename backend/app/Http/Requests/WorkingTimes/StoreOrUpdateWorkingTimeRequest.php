<?php

namespace App\Http\Requests\WorkingTimes;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdateWorkingTimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'late_after' => ['required', 'date_format:H:i', 'after_or_equal:start_time'],
            'end_time'   => ['nullable', 'date_format:H:i', 'after:start_time'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => 'Vui lòng nhập giờ bắt đầu.',
            'start_time.date_format' => 'Giờ bắt đầu phải theo định dạng HH:mm.',

            'late_after.required' => 'Vui lòng nhập thời gian trễ.',
            'late_after.date_format' => 'Thời gian trễ phải theo định dạng HH:mm.',
            'late_after.after_or_equal' => 'Thời gian trễ phải sau hoặc bằng giờ bắt đầu.',

            'end_time.date_format' => 'Giờ kết thúc phải theo định dạng HH:mm.',
            'end_time.after' => 'Giờ kết thúc phải sau giờ bắt đầu.',
        ];
    }
}
