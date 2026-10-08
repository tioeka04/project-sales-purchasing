@extends('layouts.admin')

@section('title', __('Create Product'))

@section('content')
    <x-page-header :title="__('Create Product')" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('master.products._form', ['action' => route('admin.master.products.store')])
        </div>
    </div>
@endsection
