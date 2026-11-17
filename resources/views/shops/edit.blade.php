@extends('layouts.app')

@section('title', __('messages.ui.shop_edit_title'))

@section('content')
<div class="container-narrow mx-auto">
    <div class="mb-4">
        <div class="section-kicker mb-1">{{ __('messages.ui.shop_settings') }}</div>
        <h1 class="h3 fw-bold mb-1">{{ __('messages.ui.shop_edit_title') }}</h1>
        <p class="text-muted-soft mb-0">{{ __('messages.ui.shop_edit_description') }}</p>
    </div>

    <form method="POST" action="{{ route('shops.update') }}" enctype="multipart/form-data" class="app-card p-3 p-md-5">
        @include('shops._form', ['shop' => $shop])
    </form>
</div>
@endsection
