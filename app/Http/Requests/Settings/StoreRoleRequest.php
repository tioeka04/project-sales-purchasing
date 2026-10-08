<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('role.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', $this->uniqueName()],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    protected function uniqueName(): mixed
    {
        return Rule::unique('roles', 'name');
    }
}
