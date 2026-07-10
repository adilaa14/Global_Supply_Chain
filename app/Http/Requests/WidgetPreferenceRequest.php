<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WidgetPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'widget_key' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer'],
            'is_enabled' => ['boolean'],
            'settings' => ['nullable', 'array'],
        ];
    }
}
