@extends('layouts.app')

@section('title', __('messages.ui.nav_cart'))

@section('content')
<div class="container-narrow mx-auto">
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="section-kicker mb-1">{{ __('messages.ui.cart_order') }}</div>
            <h1 class="h3 fw-bold mb-0">{{ __('messages.ui.nav_cart') }}</h1>
        </div>
        @if ($shop)
            <a class="btn btn-outline-primary btn-custom" href="{{ route('shops.public', $shop->slug) }}">{{ __('messages.ui.cart_continue_shopping') }}</a>
        @endif
    </div>

    @if ($items === [])
        <div class="app-card p-4 text-center">
            <p class="text-muted-soft mb-0">{{ __('messages.ui.cart_empty') }}</p>
        </div>
    @else
        <div class="app-card p-3 p-md-4">
            <p class="text-muted-soft mb-4">{{ __('messages.ui.shop_label') }} : <strong>{{ $shop->name }}</strong></p>

            <div class="d-flex flex-column gap-4">
                @foreach ($items as $item)
                    @php($product = $item['product'])
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 border-bottom pb-4">
                        <div class="flex-grow-1">
                            <h2 class="h5 fw-bold mb-1">{{ $product->name }}</h2>
                            <div class="text-muted-soft">
                                {{ \App\Support\PriceFormatter::format($product->price) }} {{ __('messages.ui.unit_price') }}
                            </div>
                            <div class="fw-semibold mt-1">
                                {{ __('messages.ui.subtotal') }} : {{ \App\Support\PriceFormatter::format($item['subtotal']) }} FCFA
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <form method="POST" action="{{ route('cart.products.update', $product) }}" class="d-flex align-items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <label class="visually-hidden" for="quantity-{{ $product->id }}">{{ __('messages.ui.quantity_for', ['product' => $product->name]) }}</label>
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
                                <button class="btn btn-outline-primary btn-custom" type="submit">{{ __('messages.ui.edit') }}</button>
                            </form>
                            <form method="POST" action="{{ route('cart.products.destroy', $product) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-custom" type="submit">{{ __('messages.ui.remove') }}</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 pt-4">
                <div class="h5 fw-bold mb-0">
                    {{ __('messages.ui.total') }} : {{ \App\Support\PriceFormatter::format($total) }} FCFA
                </div>
                <form method="POST" action="{{ route('cart.checkout') }}">
                    @csrf
                    <button class="btn btn-success btn-custom" type="submit">{{ __('messages.ui.checkout_whatsapp') }}</button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
