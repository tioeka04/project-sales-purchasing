@extends('layouts.admin')

@section('title', __('Users'))

@section('content')
    <x-page-header :title="__('Users')">
        <a href="{{ route('admin.settings.users.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('Create') }}</a>
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
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th style="width: 140px;">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                            <td>
                                <a href="{{ route('admin.settings.users.edit', $user) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> {{ __('Edit') }}</a>
                                @if (! $user->is(auth()->user()))
                                    <x-delete-form :action="route('admin.settings.users.destroy', $user)" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">{{ __('No data.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        </div>
    </div>
@endsection
