@extends('layouts.admin')

@section('title', __('Create Role'))

@section('content')
    <x-page-header :title="__('Create Role')" />
    @include('partials.alert')

    <div class="x_panel">
        <div class="x_content">
            @include('settings.roles._form', ['action' => route('admin.settings.roles.store')])
        </div>
    </div>
@endsection
