@extends('layouts.app')

@section('content')
<div class="container-storefront mx-auto">
    <div class="mb-4">
        <div class="section-kicker mb-1">Administration</div>
        <h1 class="h2 fw-bold mb-0">Tableau de bord administrateur</h1>
    </div>

    <div class="row g-3">
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stat-card p-3 p-md-4">
                <div class="text-muted-soft small fw-semibold">Comptes</div>
                <div class="display-6 fw-bold mb-0">{{ $userCount }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stat-card p-3 p-md-4">
                <div class="text-muted-soft small fw-semibold">Boutiques</div>
                <div class="display-6 fw-bold mb-0">{{ $shopCount }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stat-card p-3 p-md-4">
                <div class="text-muted-soft small fw-semibold">Produits</div>
                <div class="display-6 fw-bold mb-0">{{ $productCount }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stat-card p-3 p-md-4">
                <div class="text-muted-soft small fw-semibold">Visiteurs uniques estimés aujourd’hui</div>
                <div class="display-6 fw-bold mb-0" aria-label="{{ $todayVisitorCount }} {{ $todayVisitorCount === 1 ? 'visiteur unique estimé' : 'visiteurs uniques estimés' }}">{{ $todayVisitorCount }}</div>
            </div>
        </div>
    </div>

    <p class="text-muted-soft small mt-3">
        Estimation quotidienne à partir d’adresses IP pseudonymisées : une même adresse IP compte comme un seul visiteur.
        Les visiteurs connectés et les robots sont exclus.
    </p>

    <div class="row g-4 mt-2">
        <div class="col-12 col-lg-6">
            <section class="app-card p-3 p-md-4 h-100">
                <h2 class="h5 fw-bold mb-3">Visiteurs uniques — 7 derniers jours</h2>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Date</th><th class="text-end">Visiteurs</th></tr></thead>
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
                <h2 class="h5 fw-bold mb-3">Visiteurs aujourd’hui par boutique</h2>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Boutique</th><th class="text-end">Visiteurs</th></tr></thead>
                        <tbody>
                            @forelse ($shopVisitors as $shop)
                                <tr>
                                    <td>{{ $shop->name }}</td>
                                    <td class="text-end"><span aria-label="{{ $shop->visitors_today }} {{ $shop->visitors_today === 1 ? 'visiteur' : 'visiteurs' }}">{{ $shop->visitors_today }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted-soft py-3">Aucune boutique.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mt-4">
        <a class="btn btn-outline-primary btn-custom" href="{{ route('admin.users.index') }}">Gérer les comptes</a>
        <a class="btn btn-outline-primary btn-custom" href="{{ route('admin.shops.index') }}">Gérer les boutiques</a>
        <a class="btn btn-outline-primary btn-custom" href="{{ route('admin.products.index') }}">Gérer les produits</a>
    </div>
</div>
@endsection
