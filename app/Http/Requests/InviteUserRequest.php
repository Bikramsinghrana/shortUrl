<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InviteUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can('invite-users');   // add your authorization logic 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $authUser = Auth::user();

        // For update: route model binding (invitations/{user})
        $userId = $this->route('user')?->id;

        $rules = [
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'role' => 'required|in:Admin,Member',
        ];

        // SuperAdmin specific rules
        if ($authUser && $authUser->isSuperAdmin()) {
            $rules['company_option'] = 'required|in:existing,new';
            $rules['company_id']     = 'required_if:company_option,existing|nullable|exists:companies,id';
            $rules['company_name']   = 'required_if:company_option,new|nullable|string|max:255';
        }

        return $rules;
    }
}
