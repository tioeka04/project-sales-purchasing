@extends('layouts.admin')

@section('title', __('Products'))

@section('content')
    <x-page-header :title="__('Products')">
        @can('product.create')
            <a href="{{ route('admin.master.products.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('Create') }}</a>
        @endcan
    </x-page-header>
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            <form method="GET" class="form-inline" style="margin-bottom: 15px;">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Search') }}...">
                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> {{ __('Search') }}</button>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>{{ __('Product Code') }}</th>
                        <th>{{ __('Product Name') }}</th>
                        <th class="text-right">{{ __('Price') }}</th>
                        <th class="text-right">{{ __('Stock') }}</th>
                        <th style="width: 140px;">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $product->product_code }}</td>
                            <td>{{ $product->product_name }}</td>
                            <td class="text-right">{{ number_format($product->price, 2) }}</td>
                            <td class="text-right">
                                <span @class(['label', 'label-danger' => $product->stock <= \App\Models\Product::LOW_STOCK_THRESHOLD, 'label-success' => $product->stock > \App\Models\Product::LOW_STOCK_THRESHOLD])>{{ $product->stock }}</span>
                            </td>
                            <td>
                                @can('product.edit')
                                    <a href="{{ route('admin.master.products.edit', $product) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> {{ __('Edit') }}</a>
                                @endcan
                                @can('product.delete')
                                    <x-delete-form :action="route('admin.master.products.destroy', $product)" />
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">{{ __('No data.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $products->links() }}
        </div>
    </div>
@endsection
