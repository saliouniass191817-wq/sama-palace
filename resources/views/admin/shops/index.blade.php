@extends('layouts.app')

@section('content')
<div class="container-storefront mx-auto">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <div class="section-kicker mb-1">Administration</div>
            <h1 class="h2 fw-bold mb-0">Boutiques</h1>
        </div>
        <a class="btn btn-success btn-custom" href="{{ route('admin.shops.create') }}">Créer une boutique</a>
    </div>

    <div class="app-card table-responsive p-3 p-md-4">
        <table class="table align-middle mb-0">
            <thead>
                <tr><th>Boutique</th><th>Vendeur</th><th>Téléphone</th><th>Produits</th><th><span class="visually-hidden">Actions</span></th></tr>
            </thead>
            <tbody>
                @forelse ($shops as $shop)
                    <tr>
                        <td>{{ $shop->name }}</td>
                        <td>{{ $shop->user->firstName }} {{ $shop->user->lastName }}</td>
                        <td>{{ $shop->phone }}</td>
                        <td>{{ $shop->products_count }}</td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary btn-custom" href="{{ route('admin.shops.edit', $shop) }}">Modifier</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.shops.destroy', $shop) }}" onsubmit="return confirm('Supprimer cette boutique et tous ses produits ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger btn-custom" type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted-soft py-4">Aucune boutique.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($shops->hasPages())
        <div class="mt-4">{{ $shops->links() }}</div>
    @endif
</div>
@endsection
