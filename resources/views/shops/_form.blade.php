@csrf

@isset($shop)
    @method('PUT')
@endisset

<div class="mb-3">
    <label class="form-label">Nom de la boutique</label>
    <input class="form-control" name="name" value="{{ old('name', $shop->name ?? '') }}" placeholder="Awa Boutique" required>
    <div class="form-text">
    Votre URL de boutique est générée automatiquement
        @isset($shop)
            Current link: /shop/{{ $shop->slug }}
        @endisset
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea class="form-control" name="description" rows="4" placeholder="What do you sell? Where can customers find you?">{{ old('description', $shop->description ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label">Numéro WhatsApp</label>
    <input class="form-control" name="phone" value="{{ old('phone', $shop->phone ?? auth()->user()->phone) }}" placeholder="+221771234567" required>
</div>
<div class="mb-4">
    <label class="form-label">Logo</label>
    <input class="form-control" type="file" name="logo" accept="image/*">
</div>

<button class="btn btn-success btn-custom w-100 py-2">{{ isset($shop) ? 'Update shop' : 'Create shop' }}</button>
