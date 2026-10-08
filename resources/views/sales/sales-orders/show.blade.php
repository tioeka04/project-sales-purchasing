@extends('layouts.admin')

@section('title', $salesOrder->so_number)

@section('content')
    <x-page-header :title="__('Sales Order') . ' ' . $salesOrder->so_number">
        <a href="{{ route('admin.sales.sales-orders.index') }}" class="btn btn-default"><i class="fa fa-arrow-left"></i> {{ __('Back') }}</a>
    </x-page-header>
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-condensed">
                        <tr><th style="width: 40%;">{{ __('SO Number') }}</th><td>{{ $salesOrder->so_number }}</td></tr>
                        <tr><th>{{ __('Customer') }}</th><td>{{ $salesOrder->customer->name }}</td></tr>
                        <tr><th>{{ __('Order Date') }}</th><td>{{ $salesOrder->order_date->format('d M Y') }}</td></tr>
                        <tr><th>{{ __('Status') }}</th><td><span class="label {{ $salesOrder->status->badgeClass() }}">{{ $salesOrder->status->label() }}</span></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-condensed">
                        <tr><th style="width: 40%;">{{ __('Created By') }}</th><td>{{ $salesOrder->creator->name }}</td></tr>
                        <tr><th>{{ __('Approved By') }}</th><td>{{ $salesOrder->approver?->name ?? '-' }}</td></tr>
                        <tr><th>{{ __('Approved At') }}</th><td>{{ $salesOrder->approved_at?->format('d M Y H:i') ?? '-' }}</td></tr>
                        <tr><th>{{ __('Notes') }}</th><td>{{ $salesOrder->notes ?: '-' }}</td></tr>
                    </table>
                </div>
            </div>

            <h4>{{ __('Items') }}</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th class="text-right">{{ __('Quantity') }}</th>
                        <th class="text-right">{{ __('Price') }}</th>
                        <th class="text-right">{{ __('Discount') }} (%)</th>
                        <th class="text-right">{{ __('Total') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($salesOrder->lines as $line)
                        <tr>
                            <td>{{ $line->product->product_name }}</td>
                            <td class="text-right">{{ $line->quantity }}</td>
                            <td class="text-right">{{ number_format($line->price, 2) }}</td>
                            <td class="text-right">{{ $line->discount + 0 }}</td>
                            <td class="text-right">{{ number_format($line->total, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">{{ __('Grand Total') }}</th>
                        <th class="text-right">{{ number_format($salesOrder->grand_total, 2) }}</th>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <hr>
            @if ($salesOrder->status->isEditable())
                @can('sales-order.edit')
                    <a href="{{ route('admin.sales.sales-orders.edit', $salesOrder) }}" class="btn btn-info"><i class="fa fa-pencil"></i> {{ __('Edit') }}</a>
                @endcan
            @endif

            @if ($salesOrder->status->canBeApproved())
                @can('sales-order.approve')
                    <form method="POST" action="{{ route('admin.sales.sales-orders.approve', $salesOrder) }}" style="display: inline;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ __('Approve') }}</button>
                    </form>
                @endcan
            @endif

            @if ($salesOrder->status->canBeCompleted())
                @can('sales-order.edit')
                    <form method="POST" action="{{ route('admin.sales.sales-orders.deliver', $salesOrder) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success"><i class="fa fa-upload"></i> {{ __('Deliver Goods') }}</button>
                    </form>
                @endcan
            @endif

            @if ($salesOrder->status->canBeCancelled())
                @can('sales-order.edit')
                    <form method="POST" action="{{ route('admin.sales.sales-orders.cancel', $salesOrder) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-warning"><i class="fa fa-ban"></i> {{ __('Cancel Order') }}</button>
                    </form>
                @endcan
            @endif

            @if ($salesOrder->status->canBeDeleted())
                @can('sales-order.delete')
                    <x-delete-form :action="route('admin.sales.sales-orders.destroy', $salesOrder)" class="btn btn-danger" />
                @endcan
            @endif
        </div>
    </div>
@endsection
