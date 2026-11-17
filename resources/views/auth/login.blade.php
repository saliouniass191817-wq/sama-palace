@extends('layouts.app')

@section('title', __('messages.ui.login_page_title'))

@section('content')
<div class="container-narrow mx-auto">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <div class="section-kicker mb-2">{{ __('messages.ui.login_access') }}</div>
                <h1 class="display-6 fw-bold">{{ __('messages.ui.login_title') }}</h1>
                <p class="text-muted-soft mb-0">{{ __('messages.ui.login_description') }}</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="app-card p-3 p-md-5">
                @csrf

                <div class="mb-3">
                    <label class="form-label">{{ __('messages.ui.phone_number') }}</label>
                    <input class="form-control" id="login-phone" aria-label="{{ __('messages.ui.phone_number') }}" name="phone" value="{{ old('phone') }}" placeholder="+221771234567" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="login-password">{{ __('messages.ui.password') }}</label>
                    <input class="form-control" id="login-password" type="password" name="password" required>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                    <label class="form-check-label" for="remember">{{ __('messages.ui.remember_me') }}</label>
                </div>

                <button class="btn btn-success btn-custom w-100 py-2">{{ __('messages.ui.nav_login') }}</button>
                <p class="text-center text-muted-soft small mt-3 mb-0">
                    {{ __('messages.ui.new_seller') }} <a href="{{ route('register') }}">{{ __('messages.ui.footer_register') }}</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
