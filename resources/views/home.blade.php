@extends('layouts.app')

@section('content')
<div class="container-storefront mx-auto">
    <section class="app-card p-4 p-md-5 mb-4 text-center">
        <div class="section-kicker mb-2">Sama Palace</div>
        <h1 class="display-5 fw-bold mb-3">Découvrez les boutiques</h1>
        <p class="text-muted-soft mb-3">
            Parcourez les boutiques disponibles et trouvez les produits qui vous intéressent.
        </p>
        <div class="fw-semibold">{{ $shops->total() }} boutique(s) disponible(s)</div>
    </section>

    <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
        <div>
            <div class="section-kicker mb-1">Catalogue</div>
            <h2 class="h4 fw-bold mb-0">Toutes les boutiques</h2>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        @forelse ($shops as $shop)
            <div class="col-12 col-sm-6 col-lg-4">
                <a class="app-card d-flex h-100 flex-column p-3 p-md-4" href="{{ route('shops.public', $shop->slug) }}">
                    @if ($logoUrl = $shop->logo_url)
                        <img class="shop-logo w-full h-auto object-cover" src="{{ $logoUrl }}" alt="{{ $shop->name }}">
                    @else
                        <div class="shop-logo d-flex align-items-center justify-content-center fw-bold fs-4">
                            {{ strtoupper(substr($shop->name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="mt-3">
                        <h3 class="h5 fw-bold mb-2">{{ $shop->name }}</h3>
                        @if ($shop->description)
                            <p class="text-muted-soft mb-2">{{ \Illuminate\Support\Str::limit($shop->description, 120) }}</p>
                        @endif
                        <div class="small text-muted-soft">{{ $shop->products_count }} produit(s)</div>
                    </div>

                    <span class="fw-semibold mt-auto pt-3">Découvrir la boutique</span>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="app-card p-4 text-center text-muted-soft">
                    Aucune boutique n'est encore disponible.
                </div>
            </div>
        @endforelse
    </div>

    @if ($shops->hasPages())
        <div class="mt-4">{{ $shops->links() }}</div>
    @endif
</div>
@endsection
