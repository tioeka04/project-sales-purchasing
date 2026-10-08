<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('product.create');
    }

    public function rules(): array
    {
        return [
            'product_code' => ['required', 'string', 'max:20', 'alpha_dash', $this->uniqueCode()],
            'product_name' => ['required', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'stock' => ['required', 'integer', 'min:0'],
        ];
    }

    protected function uniqueCode(): mixed
    {
        return Rule::unique('products', 'product_code');
    }
}
