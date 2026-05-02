@csrf

@isset($product)
    @method('PUT')
@endisset

<div class="mb-3">
    <label class="form-label">Nom du produit</label>
    <input class="form-control" name="name" value="{{ old('name', $product->name ?? '') }}" placeholder="Wax bag" required>
</div>
<div class="mb-3">
    <label class="form-label">Prix</label>
    <div class="input-group">
        <input class="form-control" type="number" name="price" min="0" step="0.01" value="{{ old('price', $product->price ?? '') }}" required>
        <span class="input-group-text">FCFA</span>
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea class="form-control" name="description" rows="4" placeholder="Détails courts sur le produit, les tailles, les couleurs ou la disponibilité.">{{ old('description', $product->description ?? '') }}</textarea>
</div>
<div class="mb-4">
    <label class="form-label">Image</label>
    <input class="form-control" type="file" name="image" accept="image/*">
</div>

<button class="btn btn-success btn-custom w-100 py-2">{{ isset($product) ? 'Mettre à jour le produit' : 'Créer un produit' }}</button>
