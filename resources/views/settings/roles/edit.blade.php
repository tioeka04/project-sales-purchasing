@extends('layouts.admin')

@section('title', __('Edit Role'))

@section('content')
    <x-page-header :title="__('Edit Role')" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('settings.roles._form', ['action' => route('admin.settings.roles.update', $role)])
        </div>
    </div>
@endsection
