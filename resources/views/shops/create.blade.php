@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <div class="mb-4">
        <div class="section-kicker mb-1">SSetup de la boutique</div>
        <h1 class="h3 fw-bold mb-1">Créer votre boutique</h1>
        <p class="text-muted-soft mb-0">Ajoutez les informations de base dont les clients ont besoin avant de passer commande.</p>
    </div>

    <form method="POST" action="{{ route('shops.store') }}" enctype="multipart/form-data" class="app-card p-3 p-md-5">
        @include('shops._form')
    </form>
</div>
@endsection
