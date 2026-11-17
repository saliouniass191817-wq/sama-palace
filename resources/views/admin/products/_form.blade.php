@php($editing = isset($product))

<form method="POST" action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="app-card p-3 p-md-4">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-12">
            <label class="form-label" for="shop_id">{{ __('messages.ui.shop_label') }}</label>
            <select class="form-select" id="shop_id" name="shop_id" required>
                <option value="">{{ __('messages.ui.choose_shop') }}</option>
                @foreach ($shops as $shop)
                    <option value="{{ $shop->id }}" @selected((string) old('shop_id', $product->shop_id ?? '') === (string) $shop->id)>{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-8">
            <label class="form-label" for="name">{{ __('messages.ui.name') }}</label>
            <input class="form-control" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="price">{{ __('messages.ui.price_fcfa') }}</label>
            <input class="form-control" id="price" type="number" min="0" max="99999999" step="1" name="price" value="{{ old('price', $product->price ?? '') }}" required>
        </div>
        <div class="col-12">
            <label class="form-label" for="description">{{ __('messages.ui.description') }}</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-label" for="image">{{ __('messages.ui.optional_image') }}</label>
            <input class="form-control" id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button class="btn btn-success btn-custom" type="submit">{{ $editing ? __('messages.ui.save') : __('messages.ui.create_product') }}</button>
        <a class="btn btn-outline-primary btn-custom" href="{{ route('admin.products.index') }}">{{ __('messages.ui.cancel') }}</a>
    </div>
</form>
