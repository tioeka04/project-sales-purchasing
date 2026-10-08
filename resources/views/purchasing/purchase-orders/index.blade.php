@extends('layouts.admin')

@section('title', __('Purchase Orders'))

@section('content')
    <x-page-header :title="__('Purchase Orders')">
        @can('purchase-order.create')
            <a href="{{ route('admin.purchasing.purchase-orders.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('Create') }}</a>
        @endcan
    </x-page-header>
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            <form method="GET" class="form-inline" style="margin-bottom: 15px;">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Search') }}...">
                <select name="status" class="form-control">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> {{ __('Search') }}</button>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>{{ __('PO Number') }}</th>
                        <th>{{ __('Order Date') }}</th>
                        <th>{{ __('Supplier') }}</th>
                        <th class="text-right">{{ __('Grand Total') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th style="width: 140px;">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($purchaseOrders as $purchaseOrder)
                        <tr>
                            <td>{{ $purchaseOrder->po_number }}</td>
                            <td>{{ $purchaseOrder->order_date->format('d M Y') }}</td>
                            <td>{{ $purchaseOrder->supplier->name }}</td>
                            <td class="text-right">{{ number_format($purchaseOrder->grand_total, 2) }}</td>
                            <td><span class="label {{ $purchaseOrder->status->badgeClass() }}">{{ $purchaseOrder->status->label() }}</span></td>
                            <td>
                                <a href="{{ route('admin.purchasing.purchase-orders.show', $purchaseOrder) }}" class="btn btn-xs btn-default"><i class="fa fa-eye"></i> {{ __('Detail') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">{{ __('No data.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $purchaseOrders->links() }}
        </div>
    </div>
@endsection
