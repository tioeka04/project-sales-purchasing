@extends('layouts.admin')

@section('title', __('Edit Purchase Order'))

@section('content')
    <x-page-header :title="__('Edit Purchase Order') . ' ' . $purchaseOrder->po_number" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('purchasing.purchase-orders._form', ['action' => route('admin.purchasing.purchase-orders.update', $purchaseOrder)])
        </div>
    </div>
@endsection
