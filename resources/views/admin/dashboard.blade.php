@extends('layouts.app')

@section('title', __('messages.ui.admin_dashboard_title'))

@section('content')
<div class="container-storefront mx-auto">
    <div class="mb-4">
        <div class="section-kicker mb-1">{{ __('messages.ui.nav_admin') }}</div>
        <h1 class="h2 fw-bold mb-0">{{ __('messages.ui.admin_dashboard_title') }}</h1>
    </div>

    <div class="row g-3">
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stat-card p-3 p-md-4">
                <div class="text-muted-soft small fw-semibold">{{ __('messages.ui.accounts') }}</div>
                <div class="display-6 fw-bold mb-0">{{ $userCount }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stat-card p-3 p-md-4">
                <div class="text-muted-soft small fw-semibold">{{ __('messages.ui.shops') }}</div>
                <div class="display-6 fw-bold mb-0">{{ $shopCount }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stat-card p-3 p-md-4">
                <div class="text-muted-soft small fw-semibold">{{ __('messages.ui.products') }}</div>
                <div class="display-6 fw-bold mb-0">{{ $productCount }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stat-card p-3 p-md-4">
                <div class="text-muted-soft small fw-semibold">{{ __('messages.ui.estimated_visitors_today') }}</div>
                <div class="display-6 fw-bold mb-0" aria-label="{{ $todayVisitorCount }} {{ $todayVisitorCount === 1 ? __('messages.ui.estimated_visitor') : __('messages.ui.estimated_visitors') }}">{{ $todayVisitorCount }}</div>
            </div>
        </div>
    </div>

    <p class="text-muted-soft small mt-3">
        {{ __('messages.ui.visitor_estimate_explanation') }}
        {{ __('messages.ui.logged_in_and_bots_excluded') }}
    </p>

    <div class="row g-4 mt-2">
        <div class="col-12 col-lg-6">
            <section class="app-card p-3 p-md-4 h-100">
                <h2 class="h5 fw-bold mb-3">{{ __('messages.ui.visitors_last_week') }}</h2>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>{{ __('messages.ui.date') }}</th><th class="text-end">{{ __('messages.ui.visitors') }}</th></tr></thead>
                        <tbody>
                            @foreach ($dailyVisitorCounts as $daily)
                                <tr>
                                    <td>{{ $daily['date']->format('d/m/Y') }}</td>
                                    <td class="text-end">{{ $daily['count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <div class="col-12 col-lg-6">
            <section class="app-card p-3 p-md-4 h-100">
                <h2 class="h5 fw-bold mb-3">{{ __('messages.ui.visitors_by_shop_today') }}</h2>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>{{ __('messages.ui.shop_label') }}</th><th class="text-end">{{ __('messages.ui.visitors') }}</th></tr></thead>
                        <tbody>
                            @forelse ($shopVisitors as $shop)
                                <tr>
                                    <td>{{ $shop->name }}</td>
                                    <td class="text-end"><span aria-label="{{ $shop->visitors_today }} {{ $shop->visitors_today === 1 ? __('messages.ui.visitor') : __('messages.ui.visitors_lower') }}">{{ $shop->visitors_today }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted-soft py-3">{{ __('messages.ui.no_shops_period') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mt-4">
        <a class="btn btn-outline-primary btn-custom" href="{{ route('admin.users.index') }}">{{ __('messages.ui.manage_accounts') }}</a>
        <a class="btn btn-outline-primary btn-custom" href="{{ route('admin.shops.index') }}">{{ __('messages.ui.manage_shops') }}</a>
        <a class="btn btn-outline-primary btn-custom" href="{{ route('admin.products.index') }}">{{ __('messages.ui.manage_products') }}</a>
    </div>
</div>
@endsection
