@extends('layouts.admin')

@section('title', __('Edit Product'))

@section('content')
    <x-page-header :title="__('Edit Product')" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('master.products._form', ['action' => route('admin.master.products.update', $product), 'product' => $product])
        </div>
    </div>
@endsection
