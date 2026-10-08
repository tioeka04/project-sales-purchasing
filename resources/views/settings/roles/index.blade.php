@extends('layouts.admin')

@section('title', __('Roles & Permissions'))

@section('content')
    <x-page-header :title="__('Roles & Permissions')">
        <a href="{{ route('admin.settings.roles.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('Create') }}</a>
    </x-page-header>
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>{{ __('Role') }}</th>
                        <th class="text-right">{{ __('Permissions') }}</th>
                        <th class="text-right">{{ __('Users') }}</th>
                        <th style="width: 140px;">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td class="text-right">{{ $role->permissions_count }}</td>
                            <td class="text-right">{{ $role->users_count }}</td>
                            <td>
                                <a href="{{ route('admin.settings.roles.edit', $role) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> {{ __('Edit') }}</a>
                                @if ($role->name !== \Database\Seeders\RolePermissionSeeder::ADMIN)
                                    <x-delete-form :action="route('admin.settings.roles.destroy', $role)" />
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
