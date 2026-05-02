@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <div class="section-kicker mb-2">Accès vendeur</div>
                <h1 class="display-6 fw-bold">Bienvenue de nouveau</h1>
                <p class="text-muted-soft mb-0">Connectez-vous avec votre numéro de téléphone pour gérer votre boutique.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="app-card p-3 p-md-5">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Numéro de téléphone</label>
                    <input class="form-control" name="phone" value="{{ old('phone') }}" placeholder="+221771234567" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input class="form-control" type="password" name="password" required>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                </div>

                <button class="btn btn-success btn-custom w-100 py-2">Se connecter</button>
                <p class="text-center text-muted-soft small mt-3 mb-0">
                    New seller? <a href="{{ route('register') }}">Créer un compte</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
