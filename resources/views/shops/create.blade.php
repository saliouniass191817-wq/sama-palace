@extends('layouts.app')

@section('title', __('messages.ui.shop_create_title'))

@section('content')
<div class="container-narrow mx-auto">
    <h1 class="h3 fw-bold mb-3">{{ __('messages.ui.shop_create_title') }}</h1>

    <form method="POST" action="{{ route('shops.store') }}" enctype="multipart/form-data" class="bg-white border rounded-3 p-3 p-md-4">
        @include('shops._form')
    </form>
</div>
@endsection
