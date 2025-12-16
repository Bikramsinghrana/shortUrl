<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreShortUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->can('create-short-urls') ?? false;
    }

    public function rules(): array
    {
        return [
            'original_url' => 'required|url|max:2048',
            'title'        => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'short_code'   => 'nullable|string|max:10|unique:short_urls,short_code',
            'expires_at'   => 'nullable|date|after:today',
        ];
    }

    public function messages(): array
    {
        return [
            'original_url.required' => 'The original URL is required.',
            'original_url.url'      => 'Please provide a valid URL.',
            'expires_at.after'      => 'The expiration date must be in the future.',
        ];
    }
}
