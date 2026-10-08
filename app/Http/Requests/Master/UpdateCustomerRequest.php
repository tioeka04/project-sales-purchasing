<?php

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends StoreCustomerRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('customer.edit');
    }

    protected function uniqueCode(): mixed
    {
        return Rule::unique('customers', 'customer_code')->ignore($this->route('customer'));
    }
}
