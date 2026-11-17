@csrf

@isset($shop)
    @method('PUT')
@endisset

<div class="mb-3">
    <label class="form-label" for="shop-name">{{ __('messages.ui.shop_name') }}</label>
    <input class="form-control" id="shop-name" name="name" value="{{ old('name', $shop->name ?? '') }}" placeholder="{{ __('messages.ui.shop_name_placeholder') }}" required>
    <div class="form-text">
        {{ __('messages.ui.shop_url_hint') }}
        @isset($shop)
            {{ __('messages.shop.current_link') }} /shop/{{ $shop->slug }}
        @endisset
    </div>
</div>
<div class="mb-3">
    <label class="form-label" for="shop-description">{{ __('messages.ui.description') }}</label>
    <textarea class="form-control" id="shop-description" name="description" rows="4" placeholder="{{ __('messages.shop.description_placeholder') }}">{{ old('description', $shop->description ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('messages.ui.whatsapp_number') }}</label>
    <input class="form-control" id="shop-phone" aria-label="{{ __('messages.ui.whatsapp_number') }}" name="phone" value="{{ old('phone', $shop->phone ?? auth()->user()->phone) }}" placeholder="+221771234567" required>
</div>
<div class="mb-4">
    <label class="form-label" for="shop-logo">{{ __('messages.ui.logo') }}</label>
    <input class="form-control" id="shop-logo" type="file" name="logo" accept="image/jpeg,image/png,image/webp">
</div>

<button class="btn btn-success btn-custom w-100 py-2">{{ isset($shop) ? __('messages.shop.button_update') : __('messages.shop.button_create') }}</button>
