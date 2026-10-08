<?php

namespace App\Http\Requests\Settings;

use Illuminate\Validation\Rule;

class UpdateRoleRequest extends StoreRoleRequest
{
    protected function uniqueName(): mixed
    {
        return Rule::unique('roles', 'name')->ignore($this->route('role'));
    }
}
