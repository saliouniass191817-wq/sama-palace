@props([
    'product',
    'context' => 'public',
    'showClicks' => false,
])

<div class="card product-card border-0">
    <div class="product-image-wrap">
        @if ($product->image)
            <img class="card-img-top product-image" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
        @else
            <div class="product-placeholder">
                <span class="fw-semibold">Pas d'image disponible</span>
            </div>
        @endif
    </div>

    <div class="card-body d-flex flex-column p-3 p-md-4">
        <div class="d-flex justify-content-between gap-3 align-items-start mb-2">
            <h2 class="h5 fw-bold mb-0">{{ $product->name }}</h2>
            <span class="badge rounded-pill text-bg-light border">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
        </div>

        <p class="text-muted-soft small flex-grow-1 mb-3">{{ $product->description ?: 'Description coming soon.' }}</p>

        @if ($showClicks)
            <div class="small text-success fw-semibold mb-3">{{ $product->clicks_count ?? 0 }} Cliques whatsapp</div>
        @endif

        @if ($context === 'public')
            <a class="btn btn-whatsapp btn-custom w-100" href="{{ route('orders.whatsapp', $product) }}">
                Commander sur whatsapp
            </a>
        @else
            <a class="btn btn-outline-primary btn-custom w-100" href="{{ route('products.edit', $product) }}">
                Editer le produit
            </a>
        @endif
    </div>
</div>
