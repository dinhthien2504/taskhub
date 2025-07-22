<?php

namespace App\Http\Requests\Comments;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Nội dung không được để trống.',
            'content.string' => 'Nội dung phải là chuỗi.',
            'content.max' => 'Nội dung không được vượt quá 1000 ký tự.',
        ];
    }
}
