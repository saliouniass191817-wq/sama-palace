<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Micro Commerce') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7f8f5; }
        .navbar { background: #ffffff; }
        .brand-dot { width: 10px; height: 10px; background: #25d366; border-radius: 50%; display: inline-block; }
        .product-image { aspect-ratio: 4 / 3; object-fit: cover; background: #e9ecef; }
        .shop-logo { width: 72px; height: 72px; object-fit: cover; border-radius: 16px; background: #e9ecef; }
        .container-narrow { max-width: 760px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="brand-dot"></span>
                Micro Commerce
            </a>

            <div class="d-flex align-items-center gap-2">
                @auth
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard') }}">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-dark">Logout</button>
                    </form>
                @else
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('login') }}">Login</a>
                    <a class="btn btn-sm btn-success" href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">Please check the form.</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
