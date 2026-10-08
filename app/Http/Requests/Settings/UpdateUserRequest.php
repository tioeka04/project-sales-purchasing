<?php

namespace App\Http\Requests\Settings;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends StoreUserRequest
{
    protected function passwordRules(): array
    {
        return ['nullable', 'confirmed', Password::min(8)];
    }

    protected function uniqueEmail(): mixed
    {
        return Rule::unique('users', 'email')->ignore($this->route('user'));
    }
}
