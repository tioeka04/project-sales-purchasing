@extends('layouts.admin')

@section('title', __('Create Supplier'))

@section('content')
    <x-page-header :title="__('Create Supplier')" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('master.suppliers._form', ['action' => route('admin.master.suppliers.store')])
        </div>
    </div>
@endsection
