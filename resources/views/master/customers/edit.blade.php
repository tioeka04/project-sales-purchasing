@extends('layouts.admin')

@section('title', __('Edit Customer'))

@section('content')
    <x-page-header :title="__('Edit Customer')" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('master.customers._form', ['action' => route('admin.master.customers.update', $customer), 'customer' => $customer])
        </div>
    </div>
@endsection
