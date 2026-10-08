<?php

namespace App\Http\Requests\Sales;

class UpdateSalesOrderRequest extends StoreSalesOrderRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('sales-order.edit');
    }
}
