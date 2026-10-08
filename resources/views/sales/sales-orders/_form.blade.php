@php
    $rows = old('lines', isset($salesOrder)
        ? $salesOrder->lines->map(fn ($l) => $l->only(['product_id', 'quantity', 'price', 'discount']))->all()
        : [[]]);
    $nextIndex = count($rows) ? max(array_keys($rows)) + 1 : 0;
@endphp

<form method="POST" action="{{ $action }}" id="order-form">
    @csrf
    @isset($salesOrder)
        @method('PUT')
    @endisset

    <div class="row">
        <div class="col-md-6">
            <x-form.select name="customer_id" :label="__('Customer')" :options="$customers" :selected="$salesOrder->customer_id ?? null" required />
        </div>
        <div class="col-md-6">
            <x-form.input name="order_date" :label="__('Order Date')" type="date" :max="now()->toDateString()" :value="isset($salesOrder) ? $salesOrder->order_date->toDateString() : now()->toDateString()" required />
        </div>
    </div>

    <x-form.textarea name="notes" :label="__('Notes')" :value="$salesOrder->notes ?? null" />

    <h4>{{ __('Items') }}</h4>
    @error('lines') <div class="text-danger">{{ $message }}</div> @enderror
    <div class="table-responsive">
        <table class="table table-bordered" id="lines-table">
            <thead>
            <tr>
                <th style="width: 35%;">{{ __('Product') }}</th>
                <th>{{ __('Quantity') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Discount') }} (%)</th>
                <th class="text-right">{{ __('Total') }}</th>
                <th style="width: 50px;"></th>
            </tr>
            </thead>
            <tbody id="lines-body">
            @foreach ($rows as $index => $line)
                @include('sales.sales-orders._line-row', ['index' => $index, 'line' => $line])
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4" class="text-right">{{ __('Grand Total') }}</th>
                <th class="text-right" id="grand-total">0.00</th>
                <th></th>
            </tr>
            </tfoot>
        </table>
    </div>

    <button type="button" class="btn btn-default" id="add-line"><i class="fa fa-plus"></i> {{ __('Add Item') }}</button>
    <hr>
    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> {{ __('Save') }}</button>
    <a href="{{ route('admin.sales.sales-orders.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
</form>

<template id="line-template">
    @include('sales.sales-orders._line-row', ['index' => '__INDEX__', 'line' => []])
</template>

@push('scripts')
    <script>
        $(function () {
            var nextIndex = {{ $nextIndex }};
            var $body = $('#lines-body');

            function recalc() {
                var grand = 0;
                $body.find('.line-row').each(function () {
                    var qty = parseFloat($(this).find('.line-qty').val()) || 0;
                    var price = parseFloat($(this).find('.line-price').val()) || 0;
                    var discount = parseFloat($(this).find('.line-discount').val()) || 0;
                    var subtotal = qty * price;
                    var total = subtotal - (subtotal * discount / 100);
                    $(this).find('.line-total').text(total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    grand += total;
                });
                $('#grand-total').text(grand.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            }

            $('#add-line').on('click', function () {
                var html = $('#line-template').html().replace(/__INDEX__/g, nextIndex++);
                $body.append(html);
                recalc();
            });

            $body.on('click', '.remove-line', function () {
                if ($body.find('.line-row').length > 1) {
                    $(this).closest('.line-row').remove();
                    recalc();
                }
            });

            $body.on('change', '.line-product', function () {
                var price = $(this).find(':selected').data('price');
                if (price !== undefined) {
                    $(this).closest('.line-row').find('.line-price').val(price);
                }
                recalc();
            });

            $body.on('input', '.line-qty, .line-price, .line-discount', recalc);

            recalc();
        });
    </script>
@endpush
