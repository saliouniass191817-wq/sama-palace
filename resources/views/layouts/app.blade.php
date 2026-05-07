<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Micro Commerce') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
    <nav id="app-navbar" class="navbar navbar-expand-lg app-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="brand-dot"></span>
                Sama palace
            </a>

            <div class="d-flex align-items-center gap-2">
                @auth
                    <a class="btn btn-sm btn-outline-primary btn-custom" href="{{ route('dashboard') }}">Tableau de bord</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-dark btn-custom">Se déconnecter</button>
                    </form>
                @else
                    <a class="btn btn-sm btn-outline-primary btn-custom" href="{{ route('login') }}">Se connecter</a>
                    <a class="btn btn-sm btn-success btn-custom" href="{{ route('register') }}">S'inscrire</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container py-4 py-md-5">
        @if (session('success'))
            <div class="alert alert-success app-card border-0">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger app-card border-0">
                <div class="fw-semibold mb-1">Veuillez vérifier le formulaire.</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        const navbar = document.getElementById('app-navbar');
        const syncNavbarShadow = () => navbar.classList.toggle('navbar-scrolled', window.scrollY > 8);

        syncNavbarShadow();
        window.addEventListener('scroll', syncNavbarShadow, { passive: true });
    </script>
</body>
</html>
