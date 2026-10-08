<?php

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;

class UpdateProductRequest extends StoreProductRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('product.edit');
    }

    protected function uniqueCode(): mixed
    {
        return Rule::unique('products', 'product_code')->ignore($this->route('product'));
    }
}
