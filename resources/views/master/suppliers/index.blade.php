@extends('layouts.admin')

@section('title', __('Suppliers'))

@section('content')
    <x-page-header :title="__('Suppliers')">
        @can('supplier.create')
            <a href="{{ route('admin.master.suppliers.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('Create') }}</a>
        @endcan
    </x-page-header>
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            <form method="GET" class="form-inline" style="margin-bottom: 15px;">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Search') }}...">
                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> {{ __('Search') }}</button>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>{{ __('Supplier Code') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th style="width: 140px;">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->supplier_code }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>
                                @can('supplier.edit')
                                    <a href="{{ route('admin.master.suppliers.edit', $supplier) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> {{ __('Edit') }}</a>
                                @endcan
                                @can('supplier.delete')
                                    <x-delete-form :action="route('admin.master.suppliers.destroy', $supplier)" />
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">{{ __('No data.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $suppliers->links() }}
        </div>
    </div>
@endsection
