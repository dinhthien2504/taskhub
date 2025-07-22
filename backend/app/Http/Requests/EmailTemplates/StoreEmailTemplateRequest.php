<?php

namespace App\Http\Requests\EmailTemplates;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailTemplateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ];
    }
}
