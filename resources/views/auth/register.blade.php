@extends('layouts.app')

@section('title', __('messages.ui.register_title'))

@section('content')
<div class="container-narrow mx-auto">
    <div class="text-center mb-4">
        <div class="section-kicker mb-2">{{ __('messages.ui.register_title') }}</div>
        <h1 class="display-6 fw-bold">{{ __('messages.ui.register_heading') }}</h1>
        <p class="text-muted-soft mb-0">{{ __('messages.ui.register_description') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="app-card p-3 p-md-5">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('messages.ui.first_name') }}</label>
                <input class="form-control" id="register-first-name" aria-label="{{ __('messages.ui.first_name') }}" name="firstName" value="{{ old('firstName') }}" required autofocus>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="register-last-name">{{ __('messages.ui.last_name') }}</label>
                <input class="form-control" id="register-last-name" name="lastName" value="{{ old('lastName') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('messages.ui.phone') }}</label>
                <input class="form-control" id="register-phone" aria-label="{{ __('messages.ui.phone') }}" name="phone" value="{{ old('phone') }}" placeholder="+221771234567" required>
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('messages.ui.address') }}</label>
                <input class="form-control" id="register-address" aria-label="{{ __('messages.ui.address') }}" name="adress" value="{{ old('adress') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="register-password">{{ __('messages.ui.password') }}</label>
                <input class="form-control" id="register-password" type="password" name="password" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="register-password-confirmation">{{ __('messages.ui.confirm_password') }}</label>
                <input class="form-control" id="register-password-confirmation" type="password" name="password_confirmation" required>
            </div>
        </div>

        <button class="btn btn-success btn-custom w-100 py-2 mt-4">{{ __('messages.ui.footer_register') }}</button>
        <p class="text-center text-muted-soft small mt-3 mb-0">
            {{ __('messages.ui.already_registered') }} <a href="{{ route('login') }}">{{ __('messages.ui.nav_login') }}</a>
        </p>
    </form>
</div>
@endsection
