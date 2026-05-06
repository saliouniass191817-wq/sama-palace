@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    @if (! $shop)
        <div class="app-card p-4 p-md-5 text-center">
            <div class="section-kicker mb-2">Votre boutique</div>
            <h1 class="display-6 fw-bold mb-3">Commencez à vendre en ligne aujourd'hui</h1>
            <p class="text-muted-soft mb-4">Créez une boutique simple, ajoutez des produits, et partagez votre lien prêt à l'emploi sur WhatsApp.</p>
            <a class="btn btn-success btn-lg btn-custom" href="{{ route('shops.create') }}">Créer ma boutique</a>
        </div>
    @else
        @php
            $shopUrl = route('shops.public', $shop->slug);
        @endphp

        <div class="app-card p-3 p-md-4 mb-4">
            <div class="d-flex flex-column flex-sm-row gap-3 align-items-sm-center">
                @if ($shop->logo)
                    <img class="shop-logo" src="{{ Illuminate\Support\Facades\Storage::url($shop->logo) }}" alt="{{ $shop->name }}">
                @else
                    <div class="shop-logo d-flex align-items-center justify-content-center fw-bold fs-4">{{ strtoupper(substr($shop->name, 0, 1)) }}</div>
                @endif

                <div class="flex-grow-1">
                    <div class="section-kicker mb-1">Votre boutique</div>
                    <h1 class="h3 fw-bold mb-1">{{ $shop->name }}</h1>
                    <p class="text-muted-soft mb-0">{{ $shop->description ?: 'Add a short description to build trust with buyers.' }}</p>
                </div>
            </div>

            <div class="row g-2 mt-4">
                <div class="col-12 col-sm-6">
                    <a class="btn btn-success btn-custom w-100" href="{{ $shopUrl }}" target="_blank">Ouvrir la boutique publique</a>
                </div>
                <div class="col-12 col-sm-6">
                    <a class="btn btn-outline-primary btn-custom w-100" href="{{ route('shops.edit') }}">Modifier la boutique</a>
                </div>
            </div>
        </div>

        <div class="app-card p-3 p-md-4 mb-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-2 mb-3">
                <div>
                    <div class="section-kicker mb-1">Partager le lien</div>
                    <h2 class="h5 fw-bold mb-0">Envoyez votre boutique aux clients</h2>
                </div>
            </div>
            <div class="input-group">
                <input id="shop-url" class="form-control copy-input" value="{{ $shopUrl }}" readonly>
                <button id="copy-shop-url" class="btn btn-outline-success btn-custom" type="button">Copier le lien</button>
            </div>
            <div id="copy-feedback" class="form-text text-success d-none">Lien copié.</div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-4">
                <div class="stat-card p-3 p-md-4">
                    <div class="text-muted-soft small fw-semibold">Produits</div>
                    <div class="display-6 fw-bold mb-0">{{ $shop->products->count() }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="stat-card p-3 p-md-4">
                    <div class="text-muted-soft small fw-semibold">Clics sur WhatsApp</div>
                    <div class="display-6 fw-bold mb-0">{{ $shop->clicks_count }}</div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="stat-card p-3 p-md-4">
                    <div class="text-muted-soft small fw-semibold">Produit le plus cliqué</div>
                    <div class="h5 fw-bold mb-0">
                        {{ $mostClickedProduct ? $mostClickedProduct->name : 'Aucun clic pour le moment' }}
                    </div>
                    @if ($mostClickedProduct)
                        <div class="small text-success">{{ $mostClickedProduct->clicks_count }} cliques</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
            <div>
                <div class="section-kicker mb-1">Inventaire</div>
                <h2 class="h4 fw-bold mb-0">Produits</h2>
            </div>
            <a class="btn btn-success btn-custom" href="{{ route('products.create') }}">Ajouter un produit</a>
        </div>

        <div class="row g-3">
            @forelse ($shop->products as $product)
                <div class="col-12 col-sm-6">
                    <x-product-card :product="$product" context="seller" :show-clicks="true" />
                </div>
            @empty
                <div class="col-12">
                    <div class="app-card p-4 text-center text-muted-soft">No products yet.</div>
                </div>
            @endforelse
        </div>

        <script>
            document.getElementById('copy-shop-url')?.addEventListener('click', async () => {
                const input = document.getElementById('shop-url');
                const feedback = document.getElementById('copy-feedback');

                try {
                    await navigator.clipboard.writeText(input.value);
                } catch (error) {
                    input.select();
                    document.execCommand('copy');
                }

                feedback.classList.remove('d-none');
                setTimeout(() => feedback.classList.add('d-none'), 2000);
            });
        </script>
    @endif
</div>
@endsection
