@extends('layouts.admin')

@section('title', __('Edit Sales Order'))

@section('content')
    <x-page-header :title="__('Edit Sales Order') . ' ' . $salesOrder->so_number" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('sales.sales-orders._form', ['action' => route('admin.sales.sales-orders.update', $salesOrder)])
        </div>
    </div>
@endsection
