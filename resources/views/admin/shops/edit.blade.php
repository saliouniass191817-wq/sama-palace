@extends('layouts.app')

@section('title', __('messages.ui.edit_shop_admin'))

@section('content')
<div class="container-narrow mx-auto">
    <h1 class="h2 fw-bold mb-4">{{ __('messages.ui.edit_shop_admin') }}</h1>
    @include('admin.shops._form')
</div>
@endsection
