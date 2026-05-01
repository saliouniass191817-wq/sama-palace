@csrf

@isset($shop)
    @method('PUT')
@endisset

<div class="mb-3">
    <label class="form-label">Shop name</label>
    <input class="form-control" name="name" value="{{ old('name', $shop->name ?? '') }}" placeholder="Awa Boutique" required>
    <div class="form-text">
        Your public URL will be generated automatically.
        @isset($shop)
            Current link: /shop/{{ $shop->slug }}
        @endisset
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea class="form-control" name="description" rows="4">{{ old('description', $shop->description ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label">WhatsApp phone</label>
    <input class="form-control" name="phone" value="{{ old('phone', $shop->phone ?? auth()->user()->phone) }}" required>
</div>
<div class="mb-4">
    <label class="form-label">Logo</label>
    <input class="form-control" type="file" name="logo" accept="image/*">
</div>

<button class="btn btn-success w-100">{{ isset($shop) ? 'Update shop' : 'Create shop' }}</button>
