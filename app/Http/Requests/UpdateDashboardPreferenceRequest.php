<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDashboardPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'layout_id' => ['nullable', 'string', 'max:255'],
            'visible_widgets' => ['nullable', 'array'],
            'favorite_countries' => ['nullable', 'array'],
            'favorite_commodities' => ['nullable', 'array'],
            'favorite_routes' => ['nullable', 'array'],
            'default_filters' => ['nullable', 'array'],
        ];
    }
}
