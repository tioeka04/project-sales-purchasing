@extends('layouts.admin')

@section('title', __('Create Purchase Order'))

@section('content')
    <x-page-header :title="__('Create Purchase Order')" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('purchasing.purchase-orders._form', ['action' => route('admin.purchasing.purchase-orders.store')])
        </div>
    </div>
@endsection
