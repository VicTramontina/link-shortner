<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Store Link Request
 *
 * Validates link creation and update data.
 */
class StoreLinkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'original_url' => ['required', 'url', 'max:2048'],
            'title' => ['nullable', 'string', 'max:255'],
        ];

        // Slug is optional on create, but if provided must be valid
        if ($this->isMethod('POST')) {
            $rules['slug'] = ['nullable', 'string', 'min:6', 'max:8', 'alpha_num', 'unique:links,slug'];
        }

        // On update, slug must be unique except for current link
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $linkId = $this->route('link');
            $rules['slug'] = ['nullable', 'string', 'min:6', 'max:8', 'alpha_num', "unique:links,slug,{$linkId}"];
            $rules['original_url'] = ['sometimes', 'url', 'max:2048'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'original_url.required' => 'The URL is required.',
            'original_url.url' => 'Please provide a valid URL.',
            'slug.min' => 'The slug must be at least 6 characters.',
            'slug.max' => 'The slug cannot be more than 8 characters.',
            'slug.alpha_num' => 'The slug can only contain letters and numbers.',
            'slug.unique' => 'This slug is already in use.',
        ];
    }
}
