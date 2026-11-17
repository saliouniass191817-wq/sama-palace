@extends('layouts.app')

@section('title', __('messages.ui.product_edit_title'))

@section('content')
<div class="container-narrow mx-auto">
    <div class="mb-4">
        <div class="section-kicker mb-1">{{ __('messages.ui.inventory') }}</div>
        <h1 class="h3 fw-bold mb-1">{{ __('messages.ui.product_edit_title') }}</h1>
        <p class="text-muted-soft mb-0">{{ __('messages.ui.product_edit_description') }}</p>
    </div>

    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="app-card p-3 p-md-5 mb-3">
        @include('products._form', ['product' => $product])
    </form>

    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm(@js(__('messages.product.delete_confirmation')))">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger btn-custom w-100">{{ __('messages.ui.delete_product') }}</button>
    </form>
</div>
@endsection
