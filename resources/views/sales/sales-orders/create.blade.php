@extends('layouts.admin')

@section('title', __('Create Sales Order'))

@section('content')
    <x-page-header :title="__('Create Sales Order')" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('sales.sales-orders._form', ['action' => route('admin.sales.sales-orders.store')])
        </div>
    </div>
@endsection
