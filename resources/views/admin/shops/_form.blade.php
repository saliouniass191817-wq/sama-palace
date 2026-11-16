@php($editing = isset($shop))

<form method="POST" action="{{ $editing ? route('admin.shops.update', $shop) : route('admin.shops.store') }}" enctype="multipart/form-data" class="app-card p-3 p-md-4">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="user_id">Vendeur</label>
            <select class="form-select" id="user_id" name="user_id" required>
                <option value="">Choisir un compte</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((string) old('user_id', $shop->user_id ?? '') === (string) $user->id)>
                        {{ $user->firstName }} {{ $user->lastName }} — {{ $user->phone }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="name">Nom</label>
            <input class="form-control" id="name" name="name" value="{{ old('name', $shop->name ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="slug">Adresse publique (slug)</label>
            <input class="form-control" id="slug" name="slug" value="{{ old('slug', $shop->slug ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="phone">Téléphone de la boutique</label>
            <input class="form-control" id="phone" name="phone" value="{{ old('phone', $shop->phone ?? '') }}" placeholder="+221771234567" required>
        </div>
        <div class="col-12">
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $shop->description ?? '') }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-label" for="logo">Logo (facultatif)</label>
            <input class="form-control" id="logo" type="file" name="logo" accept=".jpg,.jpeg,.png,.webp">
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button class="btn btn-success btn-custom" type="submit">{{ $editing ? 'Enregistrer' : 'Créer la boutique' }}</button>
        <a class="btn btn-outline-primary btn-custom" href="{{ route('admin.shops.index') }}">Annuler</a>
    </div>
</form>
