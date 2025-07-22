<?php

namespace App\Http\Requests\EmailTemplates;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailTemplateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'subject' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ];
    }
}
