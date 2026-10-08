<?php

namespace App\Http\Requests\Sales;

use App\Models\Product;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreSalesOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('sales-order.create');
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'order_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'lines.*.quantity' => ['required', 'integer', 'min:1'],
            'lines.*.price' => ['required', 'numeric', 'min:0'],
            'lines.*.discount' => ['required', 'numeric', 'between:0,100'],
        ];
    }

    /**
     * Refuse to sell more than what is currently in stock.
     * (Re-checked again when the order is delivered.)
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $lines = collect($this->input('lines', []))->filter(fn ($line) => is_array($line));
            $products = Product::whereKey($lines->pluck('product_id')->filter()->all())->get()->keyBy('id');

            foreach ($lines as $index => $line) {
                $product = $products->get($line['product_id'] ?? null);

                if ($product && (int) ($line['quantity'] ?? 0) > $product->stock) {
                    $validator->errors()->add("lines.$index.quantity", __('Stock :name only :stock left.', [
                        'name' => $product->product_name,
                        'stock' => $product->stock,
                    ]));
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'customer_id' => __('Customer'),
            'order_date' => __('Order Date'),
            'lines' => __('Items'),
            'lines.*.product_id' => __('Product'),
            'lines.*.quantity' => __('Quantity'),
            'lines.*.price' => __('Price'),
            'lines.*.discount' => __('Discount'),
        ];
    }
}
