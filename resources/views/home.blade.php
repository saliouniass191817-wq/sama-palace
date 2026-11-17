@extends('layouts.app')

@section('title', __('messages.ui.home_title'))

@section('content')
<div class="container-storefront mx-auto">
    <section class="app-card p-4 p-md-5 mb-4 text-center">
        <div class="section-kicker mb-2">Sama Palace</div>
        <h1 class="display-5 fw-bold mb-3">{{ __('messages.ui.home_title') }}</h1>
        <p class="text-muted-soft mb-3">
            {{ __('messages.ui.home_intro') }}
        </p>
        <div class="fw-semibold">{{ __('messages.ui.shops_available', ['count' => $shops->total()]) }}</div>
    </section>

    <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
        <div>
            <div class="section-kicker mb-1">{{ __('messages.ui.catalogue') }}</div>
            <h2 class="h4 fw-bold mb-0">{{ __('messages.ui.all_shops') }}</h2>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        @forelse ($shops as $shop)
            <div class="col-12 col-sm-6 col-lg-4">
                <a class="app-card d-flex h-100 flex-column p-3 p-md-4" href="{{ route('shops.public', $shop->slug) }}">
                    @if ($logoUrl = $shop->logo_url)
                        <img class="shop-logo w-full h-auto object-cover" src="{{ $logoUrl }}" alt="{{ $shop->name }}" loading="lazy" width="512" height="512">
                    @else
                        <div class="shop-logo d-flex align-items-center justify-content-center fw-bold fs-4">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($shop->name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="mt-3">
                        <h3 class="h5 fw-bold mb-2">{{ $shop->name }}</h3>
                        @if ($shop->description)
                            <p class="text-muted-soft mb-2">{{ \Illuminate\Support\Str::limit($shop->description, 120) }}</p>
                        @endif
                        <div class="small text-muted-soft">{{ __('messages.ui.product_count', ['count' => $shop->products_count]) }}</div>
                    </div>

                    <span class="fw-semibold mt-auto pt-3">{{ __('messages.ui.discover_shop') }}</span>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="app-card p-4 text-center text-muted-soft">
                    {{ __('messages.ui.no_shops') }}
                </div>
            </div>
        @endforelse
    </div>

    @if ($shops->hasPages())
        <div class="mt-4">{{ $shops->links() }}</div>
    @endif
</div>
@endsection
