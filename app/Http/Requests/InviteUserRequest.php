<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InviteUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return Auth::check() && (bool) $user?->can('invite-users');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var User|null $authUser */
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

            'role' => 'required|in:' . implode(',', [RoleEnum::ADMIN->value, RoleEnum::MEMBER->value]),
        ];

        if ($authUser && $authUser->isSuperAdmin()) {
            $rules['role'] = 'required|in:' . RoleEnum::ADMIN->value;
            $rules['company_option'] = 'required|in:new';
            $rules['company_name'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'name'),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $slug = Str::slug((string) $value);

                    $slugExists = DB::table('companies')->where('slug', $slug)->exists();

                    if ($slugExists) {
                        $fail('This company name is already in use. Please choose a different company name.');
                    }
                },
            ];
            $rules['company_id'] = 'prohibited';
        } elseif ($authUser && $authUser->isAdmin()) {
            $rules['role'] = 'required|in:' . implode(',', [RoleEnum::ADMIN->value, RoleEnum::MEMBER->value]);
            $rules['company_option'] = 'prohibited';
            $rules['company_name'] = 'prohibited';
            $rules['company_id'] = 'prohibited';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'company_name.unique' => 'This company name is already in use. Please choose a different company name.',
        ];
    }
}
