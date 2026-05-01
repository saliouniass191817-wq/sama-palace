@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <div class="bg-white border rounded-3 p-3 p-md-4 mb-4">
        <div class="d-flex gap-3 align-items-center">
            @if ($shop->logo)
                <img class="shop-logo" src="{{ asset('storage/' . $shop->logo) }}" alt="{{ $shop->name }}">
            @else
                <div class="shop-logo d-flex align-items-center justify-content-center fw-bold">{{ strtoupper(substr($shop->name, 0, 1)) }}</div>
            @endif
            <div>
                <h1 class="h3 fw-bold mb-1">{{ $shop->name }}</h1>
                <p class="text-secondary mb-0">{{ $shop->description }}</p>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @forelse ($shop->products as $product)
            <div class="col-12 col-sm-6" id="product-{{ $product->id }}">
                <div class="card h-100 border-0 shadow-sm">
                    @if ($product->image)
                        <img class="card-img-top product-image" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h2 class="h5 fw-bold">{{ $product->name }}</h2>
                        <p class="text-secondary small flex-grow-1">{{ $product->description }}</p>
                        <div class="fw-bold mb-3">{{ number_format($product->price, 0, ',', ' ') }} FCFA</div>
                        <a class="btn btn-success w-100" href="{{ route('orders.whatsapp', $product) }}">
                            Order on WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="bg-white border rounded-3 p-4 text-center text-secondary">No products available yet.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
