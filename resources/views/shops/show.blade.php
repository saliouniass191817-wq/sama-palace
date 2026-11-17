@extends('layouts.app')

@php($previewProduct = $products->first())
@section('title', $shop->name)
@section('meta_title', $shop->name.' | '.config('app.name', 'Sama Palace'))
@section('meta_description', \Illuminate\Support\Str::limit($shop->description ?: __('messages.ui.shop_description_public'), 200))
@section('meta_image', $shop->logo_url ?: $previewProduct?->image_url ?: asset('images/logo.png'))
@section('meta_price', $previewProduct ? number_format((float) $previewProduct->price, 2, '.', '') : '')

@section('content')
<div class="container-storefront mx-auto">
    <div class="app-card p-3 p-md-5 mb-4">
        <div class="d-flex flex-column flex-sm-row gap-3 align-items-sm-center">
            @if ($logoUrl = $shop->logo_url)
                <img class="shop-logo w-full h-auto object-cover" src="{{ $logoUrl }}" alt="{{ $shop->name }}" loading="lazy" width="512" height="512">
            @else
                <div class="shop-logo d-flex align-items-center justify-content-center fw-bold fs-4">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($shop->name, 0, 1)) }}</div>
            @endif
            <div>
                <div class="section-kicker mb-1">{{ __('messages.ui.shop_description_public') }}</div>
                <h1 class="display-6 fw-bold mb-2">{{ $shop->name }}</h1>
                <p class="text-muted-soft mb-0">{{ $shop->description }}</p>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
        <div>
            <div class="section-kicker mb-1">{{ __('messages.ui.shop_catalogue') }}</div>
            <h2 class="h4 fw-bold mb-0">{{ __('messages.ui.available_products') }}</h2>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        @forelse ($products as $product)
            <div class="col-12 col-sm-6 col-lg-4" id="product-{{ $product->id }}">
                <x-product-card :product="$product" context="public" />
            </div>
        @empty
            <div class="col-12">
                <div class="app-card p-4 text-center text-muted-soft">{{ __('messages.ui.no_available_products') }}</div>
            </div>
        @endforelse
    </div>

    @if ($products->hasPages())
        <div class="mt-4">{{ $products->links() }}</div>
    @endif
</div>
@endsection
