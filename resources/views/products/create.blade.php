@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <div class="mb-4">
        <div class="section-kicker mb-1">Inventaire</div>
        <h1 class="h3 fw-bold mb-1">Ajouter un produit</h1>
        <p class="text-muted-soft mb-0">Des photos et des prix clairs aident les clients à commander plus rapidement.</p>
    </div>

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="app-card p-3 p-md-5">
        @include('products._form')
    </form>
</div>
@endsection
