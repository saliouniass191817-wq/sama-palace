@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <div class="mb-4">
        <div class="section-kicker mb-1">Paramètres de votre boutique</div>
        <h1 class="h3 fw-bold mb-1">Éditer votre boutique</h1>
        <p class="text-muted-soft mb-0">Modifiez les informations de votre boutique et les détails de contact WhatsApp.</p>
    </div>

    <form method="POST" action="{{ route('shops.update') }}" enctype="multipart/form-data" class="app-card p-3 p-md-5">
        @include('shops._form', ['shop' => $shop])
    </form>
</div>
@endsection
