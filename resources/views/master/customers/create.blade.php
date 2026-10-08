@extends('layouts.admin')

@section('title', __('Create Customer'))

@section('content')
    <x-page-header :title="__('Create Customer')" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('master.customers._form', ['action' => route('admin.master.customers.store')])
        </div>
    </div>
@endsection
