@extends('layouts.admin')

@section('title', __('Dashboard'))

@section('content')
    <x-page-header :title="__('Dashboard')" />
    @include('partials.alert')

    <div class="row tile_count">
        @can('product.view')
            <div class="col-md-3 col-sm-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-cubes"></i> {{ __('Products') }}</span>
                <div class="count">{{ $counts['products'] }}</div>
            </div>
        @endcan
        @can('supplier.view')
            <div class="col-md-3 col-sm-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-truck"></i> {{ __('Suppliers') }}</span>
                <div class="count">{{ $counts['suppliers'] }}</div>
            </div>
        @endcan
        @can('customer.view')
            <div class="col-md-3 col-sm-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-users"></i> {{ __('Customers') }}</span>
                <div class="count">{{ $counts['customers'] }}</div>
            </div>
        @endcan
        @can('purchase-order.view')
            <div class="col-md-3 col-sm-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-shopping-cart"></i> {{ __('Draft Purchase Orders') }}</span>
                <div class="count">{{ $counts['draftPurchaseOrders'] }}</div>
            </div>
        @endcan
        @can('sales-order.view')
            <div class="col-md-3 col-sm-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-money"></i> {{ __('Draft Sales Orders') }}</span>
                <div class="count">{{ $counts['draftSalesOrders'] }}</div>
            </div>
        @endcan
    </div>

    <div class="row">
        @can('product.view')
            <div class="col-md-6 col-sm-12">
                <div class="x_panel">
                    <div class="x_title"><h2>{{ __('Low Stock Products') }}</h2><div class="clearfix"></div></div>
                    <div class="x_content">
                        <table class="table table-striped">
                            <thead><tr><th>{{ __('Product Code') }}</th><th>{{ __('Product Name') }}</th><th class="text-right">{{ __('Stock') }}</th></tr></thead>
                            <tbody>
                            @forelse ($lowStockProducts as $product)
                                <tr>
                                    <td>{{ $product->product_code }}</td>
                                    <td>{{ $product->product_name }}</td>
                                    <td class="text-right"><span class="label label-danger">{{ $product->stock }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">{{ __('All products are sufficiently stocked.') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endcan

        @can('purchase-order.view')
            <div class="col-md-6 col-sm-12">
                <div class="x_panel">
                    <div class="x_title"><h2>{{ __('Recent Purchase Orders') }}</h2><div class="clearfix"></div></div>
                    <div class="x_content">
                        <table class="table table-striped">
                            <tbody>
                            @forelse ($recentPurchaseOrders as $order)
                                <tr>
                                    <td><a href="{{ route('admin.purchasing.purchase-orders.show', $order) }}">{{ $order->po_number }}</a></td>
                                    <td>{{ $order->supplier->name }}</td>
                                    <td class="text-right">{{ number_format($order->grand_total, 2) }}</td>
                                    <td><span class="label {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></td>
                                </tr>
                            @empty
                                <tr><td class="text-center">{{ __('No data.') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endcan

        @can('sales-order.view')
            <div class="col-md-6 col-sm-12">
                <div class="x_panel">
                    <div class="x_title"><h2>{{ __('Recent Sales Orders') }}</h2><div class="clearfix"></div></div>
                    <div class="x_content">
                        <table class="table table-striped">
                            <tbody>
                            @forelse ($recentSalesOrders as $order)
                                <tr>
                                    <td><a href="{{ route('admin.sales.sales-orders.show', $order) }}">{{ $order->so_number }}</a></td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td class="text-right">{{ number_format($order->grand_total, 2) }}</td>
                                    <td><span class="label {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></td>
                                </tr>
                            @empty
                                <tr><td class="text-center">{{ __('No data.') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection
