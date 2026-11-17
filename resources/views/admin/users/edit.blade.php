@extends('layouts.app')

@section('title', __('messages.ui.edit_account'))

@section('content')
<div class="container-narrow mx-auto">
    <h1 class="h2 fw-bold mb-4">{{ __('messages.ui.edit_account') }}</h1>
    @include('admin.users._form')
</div>
@endsection
