<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class ImportUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file' => 'required|file|mimes:csv,txt',
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'Vui lòng chọn file CSV.',
            'file.file' => 'File không hợp lệ.',
            'file.mimes' => 'File phải có định dạng CSV.',
        ];
    }
}
