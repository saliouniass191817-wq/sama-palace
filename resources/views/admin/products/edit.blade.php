@extends('layouts.app')

@section('title', __('messages.ui.edit_product'))

@section('content')
<div class="container-narrow mx-auto">
    <h1 class="h2 fw-bold mb-4">{{ __('messages.ui.edit_product') }}</h1>
    @include('admin.products._form')
</div>
@endsection
