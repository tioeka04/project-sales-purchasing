@extends('layouts.admin')

@section('title', $purchaseOrder->po_number)

@section('content')
    <x-page-header :title="__('Purchase Order') . ' ' . $purchaseOrder->po_number">
        <a href="{{ route('admin.purchasing.purchase-orders.index') }}" class="btn btn-default"><i class="fa fa-arrow-left"></i> {{ __('Back') }}</a>
    </x-page-header>
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-condensed">
                        <tr><th style="width: 40%;">{{ __('PO Number') }}</th><td>{{ $purchaseOrder->po_number }}</td></tr>
                        <tr><th>{{ __('Supplier') }}</th><td>{{ $purchaseOrder->supplier->name }}</td></tr>
                        <tr><th>{{ __('Order Date') }}</th><td>{{ $purchaseOrder->order_date->format('d M Y') }}</td></tr>
                        <tr><th>{{ __('Status') }}</th><td><span class="label {{ $purchaseOrder->status->badgeClass() }}">{{ $purchaseOrder->status->label() }}</span></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-condensed">
                        <tr><th style="width: 40%;">{{ __('Created By') }}</th><td>{{ $purchaseOrder->creator->name }}</td></tr>
                        <tr><th>{{ __('Approved By') }}</th><td>{{ $purchaseOrder->approver?->name ?? '-' }}</td></tr>
                        <tr><th>{{ __('Approved At') }}</th><td>{{ $purchaseOrder->approved_at?->format('d M Y H:i') ?? '-' }}</td></tr>
                        <tr><th>{{ __('Notes') }}</th><td>{{ $purchaseOrder->notes ?: '-' }}</td></tr>
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
                    @foreach ($purchaseOrder->lines as $line)
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
                        <th class="text-right">{{ number_format($purchaseOrder->grand_total, 2) }}</th>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <hr>
            @if ($purchaseOrder->status->isEditable())
                @can('purchase-order.edit')
                    <a href="{{ route('admin.purchasing.purchase-orders.edit', $purchaseOrder) }}" class="btn btn-info"><i class="fa fa-pencil"></i> {{ __('Edit') }}</a>
                @endcan
            @endif

            @if ($purchaseOrder->status->canBeApproved())
                @can('purchase-order.approve')
                    <form method="POST" action="{{ route('admin.purchasing.purchase-orders.approve', $purchaseOrder) }}" style="display: inline;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ __('Approve') }}</button>
                    </form>
                @endcan
            @endif

            @if ($purchaseOrder->status->canBeCompleted())
                @can('purchase-order.edit')
                    <form method="POST" action="{{ route('admin.purchasing.purchase-orders.receive', $purchaseOrder) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success"><i class="fa fa-download"></i> {{ __('Receive Goods') }}</button>
                    </form>
                @endcan
            @endif

            @if ($purchaseOrder->status->canBeCancelled())
                @can('purchase-order.edit')
                    <form method="POST" action="{{ route('admin.purchasing.purchase-orders.cancel', $purchaseOrder) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-warning"><i class="fa fa-ban"></i> {{ __('Cancel Order') }}</button>
                    </form>
                @endcan
            @endif

            @if ($purchaseOrder->status->canBeDeleted())
                @can('purchase-order.delete')
                    <x-delete-form :action="route('admin.purchasing.purchase-orders.destroy', $purchaseOrder)" class="btn btn-danger" />
                @endcan
            @endif
        </div>
    </div>
@endsection
