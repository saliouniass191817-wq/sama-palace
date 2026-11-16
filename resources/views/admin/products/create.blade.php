@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <h1 class="h2 fw-bold mb-4">Créer un produit</h1>
    @include('admin.products._form')
</div>
@endsection
