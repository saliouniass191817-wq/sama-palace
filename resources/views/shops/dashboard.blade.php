@extends('layouts.app')

@section('title', __('messages.ui.nav_dashboard'))

@section('content')
<div class="container-narrow mx-auto">
    @if (! $shop)
        <div class="app-card p-4 p-md-5 text-center">
            <div class="section-kicker mb-2">{{ __('messages.ui.your_shop') }}</div>
            <h1 class="display-6 fw-bold mb-3">{{ __('messages.ui.start_selling_title') }}</h1>
            <p class="text-muted-soft mb-4">{{ __('messages.ui.start_selling_description') }}</p>
            <a class="btn btn-success btn-lg btn-custom" href="{{ route('shops.create') }}">{{ __('messages.ui.create_my_shop') }}</a>
        </div>
    @else
        @php
            $shopUrl = route('shops.public', $shop->slug);
        @endphp

        <div class="app-card p-3 p-md-4 mb-4">
            <div class="d-flex flex-column flex-sm-row gap-3 align-items-sm-center">
                @if ($shop->logo)
                    <img class="shop-logo w-full h-auto object-cover" src="{{ $shop->logo_url }}" alt="{{ $shop->name }}" loading="lazy" width="512" height="512">
                @else
                    <div class="shop-logo d-flex align-items-center justify-content-center fw-bold fs-4">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($shop->name, 0, 1)) }}</div>
                @endif

                <div class="flex-grow-1">
                    <div class="section-kicker mb-1">{{ __('messages.ui.your_shop') }}</div>
                    <h1 class="h3 fw-bold mb-1">{{ $shop->name }}</h1>
                    <p class="text-muted-soft mb-0">{{ $shop->description ?: __('messages.shop.description_hint') }}</p>
                </div>
            </div>

            <div class="row g-2 mt-4">
                <div class="col-12 col-sm-6">
                    <a class="btn btn-success btn-custom w-100" href="{{ $shopUrl }}" target="_blank">{{ __('messages.ui.open_public_shop') }}</a>
                </div>
                <div class="col-12 col-sm-6">
                    <a class="btn btn-outline-primary btn-custom w-100" href="{{ route('shops.edit') }}">{{ __('messages.ui.edit_shop') }}</a>
                </div>
            </div>
        </div>

        <div class="app-card p-3 p-md-4 mb-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-2 mb-3">
                <div>
                    <div class="section-kicker mb-1">{{ __('messages.ui.share_link') }}</div>
                    <h2 class="h5 fw-bold mb-0">{{ __('messages.ui.share_shop_to_customers') }}</h2>
                </div>
            </div>
            <div class="input-group">
                <input id="shop-url" class="form-control copy-input" value="{{ $shopUrl }}" readonly>
                <button id="copy-shop-url" class="btn btn-outline-success btn-custom" type="button">{{ __('messages.ui.copy_link') }}</button>
            </div>
            <div id="copy-feedback" class="form-text text-success d-none">{{ __('messages.ui.link_copied') }}</div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="stat-card p-3 p-md-4">
                    <div class="text-muted-soft small fw-semibold">{{ __('messages.ui.products') }}</div>
                    <div class="display-6 fw-bold mb-0">{{ $shop->products_count }}</div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
            <div>
                <div class="section-kicker mb-1">{{ __('messages.ui.inventory') }}</div>
                <h2 class="h4 fw-bold mb-0">{{ __('messages.ui.products') }}</h2>
            </div>
            <a class="btn btn-success btn-custom" href="{{ route('products.create') }}">{{ __('messages.ui.add_product') }}</a>
        </div>

        <div class="row g-3">
            @forelse ($products as $product)
                <div class="col-12 col-sm-6">
                    <x-product-card :product="$product" context="seller" />
                </div>
            @empty
                <div class="col-12">
                    <div class="app-card p-4 text-center text-muted-soft">{{ __('messages.shop.no_products') }}</div>
                </div>
            @endforelse
        </div>

        @if ($products->hasPages())
            <div class="mt-4">{{ $products->links() }}</div>
        @endif

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
