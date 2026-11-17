@extends('layouts.app')

@section('title', __('messages.ui.products'))

@section('content')
<div class="container-storefront mx-auto">
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="section-kicker mb-1">{{ __('messages.ui.inventory') }}</div>
            <h1 class="h3 fw-bold mb-0">{{ __('messages.ui.products') }}</h1>
        </div>
        <a class="btn btn-success btn-custom" href="{{ route('products.create') }}">{{ __('messages.ui.add_product') }}</a>
    </div>

    <div class="row g-3 g-md-4">
        @forelse ($products as $product)
            <div class="col-12 col-sm-6 col-lg-4">
                <x-product-card :product="$product" context="seller" />
            </div>
        @empty
            <div class="col-12">
                <div class="app-card p-4 text-center text-muted-soft">{{ __('messages.ui.no_products') }}</div>
            </div>
        @endforelse
    </div>

    @if ($products->hasPages())
        <div class="mt-4">{{ $products->links() }}</div>
    @endif
</div>
@endsection
