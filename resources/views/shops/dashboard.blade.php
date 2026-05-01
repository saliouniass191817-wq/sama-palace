@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    @if (! $shop)
        <div class="bg-white border rounded-3 p-4 text-center">
            <h1 class="h4 fw-bold">Start selling online</h1>
            <p class="text-secondary">Create one simple shop, add products, and share the link on WhatsApp.</p>
            <a class="btn btn-success" href="{{ route('shops.create') }}">Create my shop</a>
        </div>
    @else
        @php
            $shopUrl = route('shops.public', $shop->slug);
        @endphp

        <div class="bg-white border rounded-3 p-3 p-md-4 mb-3">
            <div class="d-flex gap-3 align-items-center">
                @if ($shop->logo)
                    <img class="shop-logo" src="{{ asset('storage/' . $shop->logo) }}" alt="{{ $shop->name }}">
                @else
                    <div class="shop-logo d-flex align-items-center justify-content-center fw-bold">{{ strtoupper(substr($shop->name, 0, 1)) }}</div>
                @endif

                <div class="flex-grow-1">
                    <h1 class="h4 fw-bold mb-1">{{ $shop->name }}</h1>
                    <p class="text-secondary mb-0">{{ $shop->description }}</p>
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <a class="btn btn-success" href="{{ $shopUrl }}" target="_blank">Open public shop</a>
                <a class="btn btn-outline-secondary" href="{{ route('shops.edit') }}">Edit shop</a>
            </div>
        </div>

        <div class="bg-white border rounded-3 p-3 p-md-4 mb-3">
            <h2 class="h5 fw-bold mb-3">Share your shop</h2>
            <div class="input-group">
                <input id="shop-url" class="form-control" value="{{ $shopUrl }}" readonly>
                <button id="copy-shop-url" class="btn btn-outline-success" type="button">Copy link</button>
            </div>
            <div id="copy-feedback" class="form-text text-success d-none">Shop link copied.</div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="bg-white border rounded-3 p-3">
                    <div class="text-secondary small">Products</div>
                    <div class="h3 fw-bold mb-0">{{ $shop->products->count() }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-white border rounded-3 p-3">
                    <div class="text-secondary small">WhatsApp clicks</div>
                    <div class="h3 fw-bold mb-0">{{ $shop->clicks_count }}</div>
                </div>
            </div>
            <div class="col-12">
                <div class="bg-white border rounded-3 p-3">
                    <div class="text-secondary small">Most clicked product</div>
                    <div class="fw-bold">
                        {{ $mostClickedProduct ? $mostClickedProduct->name . ' (' . $mostClickedProduct->clicks_count . ')' : 'No clicks yet' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 fw-bold mb-0">Products</h2>
            <a class="btn btn-sm btn-success" href="{{ route('products.create') }}">Add product</a>
        </div>

        @forelse ($shop->products as $product)
            <div class="bg-white border rounded-3 p-3 mb-2 d-flex align-items-center gap-3">
                @if ($product->image)
                    <img class="rounded product-image" style="width: 88px;" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                @endif
                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ $product->name }}</div>
                    <div class="text-secondary">{{ number_format($product->price, 0, ',', ' ') }} FCFA</div>
                    <div class="small text-success">{{ $product->clicks_count }} WhatsApp clicks</div>
                </div>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('products.edit', $product) }}">Edit</a>
            </div>
        @empty
            <div class="bg-white border rounded-3 p-4 text-center text-secondary">No products yet.</div>
        @endforelse

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
