<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Log In') }} | {{ config('app.name') }}</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/gentelella/css/custom.min.css') }}" rel="stylesheet">
</head>
<body class="login">
<div class="login_wrapper">
    <div class="animate form login_form">
        <section class="login_content">
            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <h1>{{ config('app.name') }}</h1>

                @error('email')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <div>
                    <input type="email" name="email" class="form-control" placeholder="{{ __('Email') }}" value="{{ old('email') }}" required autofocus>
                </div>
                <div>
                    <input type="password" name="password" class="form-control" placeholder="{{ __('Password') }}" required>
                </div>
                <div style="text-align: left; margin-bottom: 10px;">
                    <label><input type="checkbox" name="remember" value="1"> {{ __('Remember me') }}</label>
                </div>
                <div>
                    <button type="submit" class="btn btn-default submit">{{ __('Log In') }}</button>
                </div>
            </form>
        </section>
    </div>
</div>
</body>
</html>
