@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <div class="text-center mb-4">
        <div class="section-kicker mb-2">Create shop</div>
        <h1 class="display-6 fw-bold">Create your seller account</h1>
        <p class="text-muted-soft mb-0">Use your phone number to create and manage a simple online shop.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="app-card p-3 p-md-5">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">First name</label>
                <input class="form-control" name="firstName" value="{{ old('firstName') }}" required autofocus>
            </div>
            <div class="col-md-6">
                <label class="form-label">Last name</label>
                <input class="form-control" name="lastName" value="{{ old('lastName') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">Phone</label>
                <input class="form-control" name="phone" value="{{ old('phone') }}" placeholder="+221771234567" required>
            </div>
            <div class="col-12">
                <label class="form-label">Address</label>
                <input class="form-control" name="adress" value="{{ old('adress') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Password</label>
                <input class="form-control" type="password" name="password" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirm password</label>
                <input class="form-control" type="password" name="password_confirmation" required>
            </div>
        </div>

        <button class="btn btn-success btn-custom w-100 py-2 mt-4">Create account</button>
        <p class="text-center text-muted-soft small mt-3 mb-0">
            Already registered? <a href="{{ route('login') }}">Login</a>
        </p>
    </form>
</div>
@endsection
