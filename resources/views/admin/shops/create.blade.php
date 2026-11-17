@extends('layouts.app')

@section('title', __('messages.ui.create_shop'))

@section('content')
<div class="container-narrow mx-auto">
    <h1 class="h2 fw-bold mb-4">{{ __('messages.ui.create_shop') }}</h1>
    @include('admin.shops._form')
</div>
@endsection
