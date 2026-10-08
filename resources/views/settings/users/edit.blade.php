@extends('layouts.admin')

@section('title', __('Edit User'))

@section('content')
    <x-page-header :title="__('Edit User')" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('settings.users._form', ['action' => route('admin.settings.users.update', $user)])
        </div>
    </div>
@endsection
