@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <div class="mb-4">
        <h1 class="h3 fw-bold">Welcome back</h1>
        <p class="text-secondary mb-0">Login with your phone number.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="bg-white border rounded-3 p-3 p-md-4">
        @csrf

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input class="form-control" name="phone" value="{{ old('phone') }}" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required>
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>

        <button class="btn btn-success w-100">Login</button>
        <p class="text-center text-secondary small mt-3 mb-0">
            New seller? <a href="{{ route('register') }}">Create account</a>
        </p>
    </form>
</div>
@endsection
