@extends('layouts.app')

@section('content')
<div class="container-narrow mx-auto">
    <h1 class="h3 fw-bold mb-3">Veuillez remplir les informations de votre boutique</h1>

    <form method="POST" action="{{ route('shops.store') }}" enctype="multipart/form-data" class="bg-white border rounded-3 p-3 p-md-4">
        @include('shops._form')
    </form>
</div>
@endsection
