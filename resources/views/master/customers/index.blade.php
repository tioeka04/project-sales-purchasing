@extends('layouts.admin')

@section('title', __('Customers'))

@section('content')
    <x-page-header :title="__('Customers')">
        @can('customer.create')
            <a href="{{ route('admin.master.customers.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('Create') }}</a>
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
                        <th>{{ __('Customer Code') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th style="width: 140px;">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>{{ $customer->customer_code }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>
                                @can('customer.edit')
                                    <a href="{{ route('admin.master.customers.edit', $customer) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> {{ __('Edit') }}</a>
                                @endcan
                                @can('customer.delete')
                                    <x-delete-form :action="route('admin.master.customers.destroy', $customer)" />
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">{{ __('No data.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $customers->links() }}
        </div>
    </div>
@endsection
