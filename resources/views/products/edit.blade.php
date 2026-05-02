@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <div class="mb-4">
        <div class="section-kicker mb-1">Inventaire</div>
        <h1 class="h3 fw-bold mb-1">Modifier le produit</h1>
        <p class="text-muted-soft mb-0">Gardez les détails du produit à jour pour de meilleures conversions sur WhatsApp.</p>
    </div>

    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="app-card p-3 p-md-5 mb-3">
        @include('products._form', ['product' => $product])
    </form>

    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger btn-custom w-100">Supprimer le produit</button>
    </form>
</div>
@endsection
