@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 fw-bold mb-0">Products</h1>
        <a class="btn btn-success" href="{{ route('products.create') }}">Add product</a>
    </div>

    @forelse ($products as $product)
        <div class="bg-white border rounded-3 p-3 mb-2 d-flex align-items-center gap-3">
            @if ($product->image)
                <img class="rounded product-image" style="width: 88px;" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            @endif
            <div class="flex-grow-1">
                <div class="fw-semibold">{{ $product->name }}</div>
                <div class="text-secondary">{{ number_format($product->price, 0, ',', ' ') }} FCFA</div>
            </div>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('products.edit', $product) }}">Edit</a>
        </div>
    @empty
        <div class="bg-white border rounded-3 p-4 text-center text-secondary">No products yet.</div>
    @endforelse
</div>
@endsection
