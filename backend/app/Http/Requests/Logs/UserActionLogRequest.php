<?php

namespace App\Http\Requests\Logs;

use Illuminate\Foundation\Http\FormRequest;

class UserActionLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:' . now()->year + 5],
        ];
    }
}
