@extends('layouts.app')

@section('content')
<div class="container-storefront mx-auto">
    <div class="app-card p-3 p-md-5 mb-4">
        <div class="d-flex flex-column flex-sm-row gap-3 align-items-sm-center">
            @if ($shop->logo)
                <img class="shop-logo w-full h-auto object-cover" src="{{ asset('storage/' . $shop->logo) }}" alt="{{ $shop->name }}">
            @else
                <div class="shop-logo d-flex align-items-center justify-content-center fw-bold fs-4">{{ strtoupper(substr($shop->name, 0, 1)) }}</div>
            @endif
            <div>
                <div class="section-kicker mb-1">Boutique en ligne</div>
                <h1 class="display-6 fw-bold mb-2">{{ $shop->name }}</h1>
                <p class="text-muted-soft mb-0">{{ $shop->description }}</p>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
        <div>
            <div class="section-kicker mb-1">Catalogue</div>
            <h2 class="h4 fw-bold mb-0">Produits disponibles</h2>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        @forelse ($shop->products as $product)
            <div class="col-12 col-sm-6 col-lg-4" id="product-{{ $product->id }}">
                <x-product-card :product="$product" context="public" />
            </div>
        @empty
            <div class="col-12">
                <div class="app-card p-4 text-center text-muted-soft">Aucun produit disponible pour le moment.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
