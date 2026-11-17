@props([
    'product',
    'context' => 'public',
])

<div class="card product-card border-0">
    <div class="product-image-wrap">
        @if ($imageUrl = $product->image_url)
            <img class="card-img-top product-image w-full h-auto object-cover" src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy" width="1200" height="1200">
        @else
            <div class="product-placeholder">
                <span class="fw-semibold">{{ __('messages.ui.no_image') }}</span>
            </div>
        @endif
    </div>

    <div class="card-body d-flex flex-column p-3 p-md-4">
        <div class="d-flex justify-content-between gap-3 align-items-start mb-2">
            <h2 class="h5 fw-bold mb-0">{{ $product->name }}</h2>
            <span class="badge rounded-pill text-bg-light border">{{ \App\Support\PriceFormatter::format($product->price) }} FCFA</span>
        </div>

        <p class="text-muted-soft small flex-grow-1 mb-3">{{ $product->description ?: __('messages.product.description_coming_soon') }}</p>

        @if ($context === 'public')
            <form method="POST" action="{{ route('cart.products.store', $product) }}" class="mb-2">
                @csrf
                <button class="btn btn-success btn-custom w-100" type="submit">{{ __('messages.ui.add_to_cart') }}</button>
            </form>
            <a class="btn btn-whatsapp btn-custom w-100" href="{{ route('orders.whatsapp', $product) }}">
                {{ __('messages.ui.order_product_alone') }}
            </a>
        @else
            <a class="btn btn-outline-primary btn-custom w-100" href="{{ route('products.edit', $product) }}">
                {{ __('messages.product.edit') }}
            </a>
        @endif
    </div>
</div>
