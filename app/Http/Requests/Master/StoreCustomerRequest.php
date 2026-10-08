<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('customer.create');
    }

    public function rules(): array
    {
        return [
            'customer_code' => ['required', 'string', 'max:20', 'alpha_dash', $this->uniqueCode()],
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function uniqueCode(): mixed
    {
        return Rule::unique('customers', 'customer_code');
    }
}
