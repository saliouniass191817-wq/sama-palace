@extends('layouts.app')

@section('content')
<div class="container-storefront mx-auto">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <div class="section-kicker mb-1">Administration</div>
            <h1 class="h2 fw-bold mb-0">Comptes utilisateurs</h1>
        </div>
        <a class="btn btn-success btn-custom" href="{{ route('admin.users.create') }}">Créer un compte</a>
    </div>

    <div class="app-card table-responsive p-3 p-md-4">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Rôle</th>
                    <th>Boutique</th>
                    <th><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->firstName }} {{ $user->lastName }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->isAdmin() ? 'Administrateur' : 'Vendeur' }}</td>
                        <td>{{ $user->shop?->name ?? '—' }}</td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary btn-custom" href="{{ route('admin.users.edit', $user) }}">Modifier</a>
                            @unless ($user->is(auth()->user()) || ($user->isAdmin() && $adminCount <= 1))
                                <form class="d-inline" method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Supprimer ce compte et, le cas échéant, sa boutique et ses produits ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger btn-custom" type="submit">Supprimer</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted-soft py-4">Aucun compte.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="mt-4">{{ $users->links() }}</div>
    @endif
</div>
@endsection
