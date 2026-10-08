<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) | {{ config('app.name') }}</title>

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/nprogress/nprogress.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/gentelella/css/custom.min.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        @include('partials.navigation')
        @include('partials.topbar')

        <div class="right_col" role="main">
            @yield('content')
        </div>

        @include('partials.footer')
    </div>
</div>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/nprogress/nprogress.js') }}"></script>
<script src="{{ asset('vendor/gentelella/js/custom.min.js') }}"></script>
@stack('scripts')
</body>
</html>
