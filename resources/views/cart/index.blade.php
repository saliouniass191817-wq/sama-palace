@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="section-kicker mb-1">Votre commande</div>
            <h1 class="h3 fw-bold mb-0">Panier</h1>
        </div>
        @if ($shop)
            <a class="btn btn-outline-primary btn-custom" href="{{ route('shops.public', $shop->slug) }}">Continuer mes achats</a>
        @endif
    </div>

    @if ($items === [])
        <div class="app-card p-4 text-center">
            <p class="text-muted-soft mb-0">Votre panier est vide.</p>
        </div>
    @else
        <div class="app-card p-3 p-md-4">
            <p class="text-muted-soft mb-4">Boutique : <strong>{{ $shop->name }}</strong></p>

            <div class="d-flex flex-column gap-4">
                @foreach ($items as $item)
                    @php($product = $item['product'])
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 border-bottom pb-4">
                        <div class="flex-grow-1">
                            <h2 class="h5 fw-bold mb-1">{{ $product->name }}</h2>
                            <div class="text-muted-soft">
                                {{ \App\Support\PriceFormatter::format($product->price) }} FCFA l'unité
                            </div>
                            <div class="fw-semibold mt-1">
                                Sous-total : {{ \App\Support\PriceFormatter::format($item['subtotal']) }} FCFA
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <form method="POST" action="{{ route('cart.products.update', $product) }}" class="d-flex align-items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <label class="visually-hidden" for="quantity-{{ $product->id }}">Quantité de {{ $product->name }}</label>
                                <input
                                    class="form-control"
                                    id="quantity-{{ $product->id }}"
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="99"
                                    value="{{ $item['quantity'] }}"
                                    required
                                    style="width: 84px"
                                >
                                <button class="btn btn-outline-primary btn-custom" type="submit">Modifier</button>
                            </form>
                            <form method="POST" action="{{ route('cart.products.destroy', $product) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-custom" type="submit">Retirer</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 pt-4">
                <div class="h5 fw-bold mb-0">
                    Total : {{ \App\Support\PriceFormatter::format($total) }} FCFA
                </div>
                <form method="POST" action="{{ route('cart.checkout') }}">
                    @csrf
                    <button class="btn btn-success btn-custom" type="submit">Commander sur WhatsApp</button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
