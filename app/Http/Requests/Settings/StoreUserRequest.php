<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('user.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', $this->uniqueEmail()],
            'password' => $this->passwordRules(),
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }

    protected function passwordRules(): array
    {
        return ['required', 'confirmed', Password::min(8)];
    }

    protected function uniqueEmail(): mixed
    {
        return Rule::unique('users', 'email');
    }
}
