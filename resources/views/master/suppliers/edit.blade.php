@extends('layouts.admin')

@section('title', __('Edit Supplier'))

@section('content')
    <x-page-header :title="__('Edit Supplier')" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('master.suppliers._form', ['action' => route('admin.master.suppliers.update', $supplier), 'supplier' => $supplier])
        </div>
    </div>
@endsection
