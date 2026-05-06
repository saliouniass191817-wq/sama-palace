@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <div class="text-center mb-4">
        <div class="section-kicker mb-2">Créer un compte</div>
        <h1 class="display-6 fw-bold">Créer votre compte vendeur</h1>
        <p class="text-muted-soft mb-0">Utilisez votre numéro de téléphone pour créer et gérer une boutique en ligne simple.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="app-card p-3 p-md-5">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Prénom</label>
                <input class="form-control" name="firstName" value="{{ old('firstName') }}" required autofocus>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nom de famille</label>
                <input class="form-control" name="lastName" value="{{ old('lastName') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">Téléphone</label>
                <input class="form-control" name="phone" value="{{ old('phone') }}" placeholder="+221771234567" required>
            </div>
            <div class="col-12">
                <label class="form-label">Addresse</label>
                <input class="form-control" name="adress" value="{{ old('adress') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Mot de passe</label>
                <input class="form-control" type="password" name="password" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirmer le mot de passe</label>
                <input class="form-control" type="password" name="password_confirmation" required>
            </div>
        </div>

        <button class="btn btn-success btn-custom w-100 py-2 mt-4">Créer un compte</button>
        <p class="text-center text-muted-soft small mt-3 mb-0">
            Déjà inscrit? <a href="{{ route('login') }}">Se connecter</a>
        </p>
    </form>
</div>
@endsection
