@extends('layouts.app')

@section('content')
<div class="container-storefront mx-auto">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <div class="section-kicker mb-1">Administration</div>
            <h1 class="h2 fw-bold mb-0">Produits</h1>
        </div>
        <a class="btn btn-success btn-custom" href="{{ route('admin.products.create') }}">Créer un produit</a>
    </div>

    <div class="app-card table-responsive p-3 p-md-4">
        <table class="table align-middle mb-0">
            <thead>
                <tr><th>Produit</th><th>Boutique</th><th>Prix</th><th><span class="visually-hidden">Actions</span></th></tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->shop->name }}</td>
                        <td>{{ \App\Support\PriceFormatter::format($product->price) }} FCFA</td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary btn-custom" href="{{ route('admin.products.edit', $product) }}">Modifier</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Supprimer ce produit ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger btn-custom" type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted-soft py-4">Aucun produit.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($products->hasPages())
        <div class="mt-4">{{ $products->links() }}</div>
    @endif
</div>
@endsection
