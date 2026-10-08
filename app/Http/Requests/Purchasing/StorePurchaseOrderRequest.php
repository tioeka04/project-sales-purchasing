<?php

namespace App\Http\Requests\Purchasing;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('purchase-order.create');
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'order_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'lines.*.quantity' => ['required', 'integer', 'min:1'],
            'lines.*.price' => ['required', 'numeric', 'min:0'],
            'lines.*.discount' => ['required', 'numeric', 'between:0,100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'supplier_id' => __('Supplier'),
            'order_date' => __('Order Date'),
            'lines' => __('Items'),
            'lines.*.product_id' => __('Product'),
            'lines.*.quantity' => __('Quantity'),
            'lines.*.price' => __('Price'),
            'lines.*.discount' => __('Discount'),
        ];
    }
}
