<?php

namespace App\Http\Requests\Central;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'slug' => [
                'required',
                'string',
                'alpha_dash',
                'max:50',
                'lowercase',
                Rule::unique('tenants', 'id'),
            ],

            'admin_name' => [
                'required',
                'string',
                'max:255',
            ],

            'admin_email' => [
                'required',
                'email',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.alpha_dash' => 'The tenant slug may only contain letters, numbers, dashes, and underscores.',
            'slug.lowercase' => 'The tenant slug must be lowercase.',
            'slug.unique' => 'This tenant slug is already in use.',
        ];
    }
}
