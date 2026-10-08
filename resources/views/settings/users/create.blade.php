@extends('layouts.admin')

@section('title', __('Create User'))

@section('content')
    <x-page-header :title="__('Create User')" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('settings.users._form', ['action' => route('admin.settings.users.store')])
        </div>
    </div>
@endsection
