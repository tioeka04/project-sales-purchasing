<?php

namespace App\Http\Requests\Purchasing;

class UpdatePurchaseOrderRequest extends StorePurchaseOrderRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('purchase-order.edit');
    }
}
