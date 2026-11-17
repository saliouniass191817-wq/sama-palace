@csrf

@isset($product)
    @method('PUT')
@endisset

<div class="mb-3">
    <label class="form-label" for="product-name">{{ __('messages.ui.product_name') }}</label>
    <input class="form-control" id="product-name" name="name" value="{{ old('name', $product->name ?? '') }}" placeholder="{{ __('messages.product.name_placeholder') }}" required>
</div>
<div class="mb-3">
    <label class="form-label" for="product-price">{{ __('messages.ui.price') }}</label>
    <div class="input-group">
        <input class="form-control" id="product-price" type="number" name="price" min="0" step="1" value="{{ old('price', isset($product) ? rtrim(rtrim((string) $product->price, '0'), '.') : '') }}" required>
        <span class="input-group-text">FCFA</span>
    </div>
</div>
<div class="mb-3">
    <label class="form-label" for="product-description">{{ __('messages.ui.description') }}</label>
    <textarea class="form-control" name="description" rows="4" placeholder="{{ __('messages.ui.product_description_placeholder') }}">{{ old('description', $product->description ?? '') }}</textarea>
</div>
<div class="mb-4">
    <label class="form-label" for="product-image">{{ __('messages.ui.image') }}</label>
    <input class="form-control" id="product-image" type="file" name="image" accept="image/jpeg,image/png,image/webp">
</div>

<button class="btn btn-success btn-custom w-100 py-2">{{ isset($product) ? __('messages.ui.product_button_update') : __('messages.ui.create_product') }}</button>
