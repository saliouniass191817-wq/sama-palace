@extends('layouts.app')

@section('content')
<div class="container-storefront mx-auto">
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="section-kicker mb-1">Inventaire</div>
            <h1 class="h3 fw-bold mb-0">Produits</h1>
        </div>
        <a class="btn btn-success btn-custom" href="{{ route('products.create') }}">Ajouter un produit</a>
    </div>

    <div class="row g-3 g-md-4">
        @forelse ($products as $product)
            <div class="col-12 col-sm-6 col-lg-4">
                <x-product-card :product="$product" context="seller" />
            </div>
        @empty
            <div class="col-12">
                <div class="app-card p-4 text-center text-muted-soft">Pas de produits disponibles pour le moment.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
