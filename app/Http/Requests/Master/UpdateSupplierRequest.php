<?php

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends StoreSupplierRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('supplier.edit');
    }

    protected function uniqueCode(): mixed
    {
        return Rule::unique('suppliers', 'supplier_code')->ignore($this->route('supplier'));
    }
}
