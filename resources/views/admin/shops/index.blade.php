@extends('layouts.app')

@section('title', __('messages.ui.admin_shops'))

@section('content')
<div class="container-storefront mx-auto">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <div class="section-kicker mb-1">{{ __('messages.ui.nav_admin') }}</div>
            <h1 class="h2 fw-bold mb-0">{{ __('messages.ui.admin_shops') }}</h1>
        </div>
        <a class="btn btn-success btn-custom" href="{{ route('admin.shops.create') }}">{{ __('messages.ui.create_shop') }}</a>
    </div>

    <div class="app-card table-responsive p-3 p-md-4">
        <table class="table align-middle mb-0">
            <thead>
                <tr><th>{{ __('messages.ui.shop_label') }}</th><th>{{ __('messages.ui.seller') }}</th><th>{{ __('messages.ui.phone') }}</th><th>{{ __('messages.ui.products') }}</th><th><span class="visually-hidden">{{ __('messages.ui.actions') }}</span></th></tr>
            </thead>
            <tbody>
                @forelse ($shops as $shop)
                    <tr>
                        <td>{{ $shop->name }}</td>
                        <td>{{ $shop->user->first_name }} {{ $shop->user->last_name }}</td>
                        <td>{{ $shop->phone }}</td>
                        <td>{{ $shop->products_count }}</td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary btn-custom" href="{{ route('admin.shops.edit', $shop) }}">{{ __('messages.ui.edit') }}</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.shops.destroy', $shop) }}" onsubmit="return confirm(@js(__('messages.ui.delete_shop_confirmation')))">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger btn-custom" type="submit">{{ __('messages.ui.remove') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted-soft py-4">{{ __('messages.ui.no_shops') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($shops->hasPages())
        <div class="mt-4">{{ $shops->links() }}</div>
    @endif
</div>
@endsection
