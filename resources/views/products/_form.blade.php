@csrf

@isset($product)
    @method('PUT')
@endisset

<div class="mb-3">
    <label class="form-label">Product name</label>
    <input class="form-control" name="name" value="{{ old('name', $product->name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Price</label>
    <input class="form-control" type="number" name="price" min="0" step="0.01" value="{{ old('price', $product->price ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea class="form-control" name="description" rows="4">{{ old('description', $product->description ?? '') }}</textarea>
</div>
<div class="mb-4">
    <label class="form-label">Image</label>
    <input class="form-control" type="file" name="image" accept="image/*">
</div>

<button class="btn btn-success w-100">{{ isset($product) ? 'Update product' : 'Create product' }}</button>
