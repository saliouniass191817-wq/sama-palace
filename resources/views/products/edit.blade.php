@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <h1 class="h3 fw-bold mb-3">Edit product</h1>

    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="bg-white border rounded-3 p-3 p-md-4 mb-3">
        @include('products._form', ['product' => $product])
    </form>

    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger w-100">Delete product</button>
    </form>
</div>
@endsection
