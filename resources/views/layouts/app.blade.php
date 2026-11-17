<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', __('messages.ui.site_description'))">
    <meta property="og:site_name" content="{{ config('app.name', 'Sama Palace') }}">
    <meta property="og:title" content="@yield('meta_title', trim($__env->yieldContent('title')) ?: config('app.name', 'Sama Palace'))">
    <meta property="og:description" content="@yield('meta_description', __('messages.ui.site_description'))">
    <meta property="og:image" content="@yield('meta_image', asset('images/logo.png'))">
    <meta property="og:url" content="@yield('meta_url', url()->current())">
    <meta property="og:type" content="@yield('meta_type', 'website')">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('meta_title', trim($__env->yieldContent('title')) ?: config('app.name', 'Sama Palace'))">
    <meta name="twitter:description" content="@yield('meta_description', __('messages.ui.site_description'))">
    <meta name="twitter:image" content="@yield('meta_image', asset('images/logo.png'))">
    @if (trim($__env->yieldContent('meta_price')) !== '')
        <meta property="product:price:amount" content="@yield('meta_price')">
        <meta property="product:price:currency" content="@yield('meta_currency', 'XOF')">
    @endif
    <title>@hasSection('title')@yield('title') | @endif{{ config('app.name', 'Sama Palace') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    @vite('resources/css/app.css')
</head>
<body class="d-flex min-vh-100 flex-column">
    <nav id="app-navbar" class="navbar navbar-expand-lg app-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="brand-dot"></span>
                Sama Palace
            </a>

            <div class="d-flex align-items-center gap-2">
                @auth
                    @if (auth()->user()->isAdmin())
                        <a class="btn btn-sm btn-outline-primary btn-custom" href="{{ route('admin.dashboard') }}">{{ __('messages.ui.nav_admin') }}</a>
                    @else
                        <a class="btn btn-sm btn-outline-primary btn-custom" href="{{ route('dashboard') }}">{{ __('messages.ui.nav_dashboard') }}</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-dark btn-custom">{{ __('messages.ui.nav_logout') }}</button>
                    </form>
                @else
                    @php($cartCount = array_sum(session('whatsapp_cart.items', [])))
                    <a class="btn btn-sm btn-outline-primary btn-custom" href="{{ route('cart.index') }}">
                        {{ __('messages.ui.nav_cart') }} <span class="badge text-bg-light">{{ $cartCount }}</span>
                    </a>
                    <a class="btn btn-sm btn-outline-primary btn-custom" href="{{ route('login') }}">{{ __('messages.ui.nav_login') }}</a>
                    <a class="btn btn-sm btn-success btn-custom" href="{{ route('register') }}">{{ __('messages.ui.nav_register') }}</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container flex-grow-1 py-4 py-md-5">
        @if (session('success'))
            <div class="alert alert-success app-card border-0">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger app-card border-0">
                <div class="fw-semibold mb-1">{{ __('messages.ui.verify_form') }}</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="app-footer mt-auto">
        <div class="container py-4 py-md-5">
            <div class="app-footer-panel p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-md-7">
                        <a class="app-footer-brand d-inline-flex align-items-center gap-2 fw-bold" href="{{ route('home') }}">
                            <span class="brand-dot"></span>
                            Sama Palace
                        </a>
                        <p class="app-footer-description mb-0 mt-3">
                            {{ __('messages.ui.footer_description') }}
                        </p>
                    </div>
                    <div class="col-md-5">
                        <nav class="app-footer-links d-flex flex-wrap gap-3 justify-content-md-end" aria-label="{{ __('messages.ui.footer_navigation') }}">
                            <a href="{{ route('home') }}">{{ __('messages.ui.footer_home') }}</a>
                            @auth
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}">{{ __('messages.ui.nav_admin') }}</a>
                                @else
                                    <a href="{{ route('dashboard') }}">{{ __('messages.ui.nav_dashboard') }}</a>
                                @endif
                            @else
                                <a href="{{ route('login') }}">{{ __('messages.ui.nav_login') }}</a>
                                <a href="{{ route('register') }}">{{ __('messages.ui.footer_register') }}</a>
                            @endauth
                        </nav>
                    </div>
                </div>
                <div class="app-footer-meta d-flex flex-column flex-sm-row justify-content-between gap-2 mt-4 pt-3">
                    <span>&copy; {{ now()->year }} Sama Palace</span>
                    <span>{{ __('messages.ui.footer_tagline') }}</span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const navbar = document.getElementById('app-navbar');
        const syncNavbarShadow = () => navbar.classList.toggle('navbar-scrolled', window.scrollY > 8);

        syncNavbarShadow();
        window.addEventListener('scroll', syncNavbarShadow, { passive: true });
    </script>
</body>
</html>
