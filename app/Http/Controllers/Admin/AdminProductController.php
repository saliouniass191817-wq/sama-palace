<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageProductRequest;
use App\Models\Product;
use App\Models\Shop;
use App\Support\PublicMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class AdminProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::with('shop')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', ['shops' => Shop::orderBy('name')->get()]);
    }

    public function store(ManageProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $image = $request->file('image');
        unset($validated['image']);

        if ($image) {
            PublicMedia::storeAndPersist(
                $image,
                'products',
                'image',
                function (string $path) use ($validated) {
                    $validated['image'] = $path;

                    return Product::create($validated);
                },
            );
        } else {
            Product::create($validated);
        }

        return redirect()->route('admin.products.index')->with('success', 'Le produit a été créé.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'shops' => Shop::orderBy('name')->get(),
        ]);
    }

    public function update(ManageProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();
        $image = $request->file('image');
        unset($validated['image']);

        if ($image) {
            PublicMedia::storeAndPersist(
                $image,
                'products',
                'image',
                function (string $path) use ($product, $validated) {
                    $validated['image'] = $path;

                    return $product->update($validated);
                },
                $product->image,
            );
        } elseif (! $product->update($validated)) {
            throw new RuntimeException('Le produit n’a pas pu être mis à jour.');
        }

        return redirect()->route('admin.products.index')->with('success', 'Le produit a été mis à jour.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $image = $product->image;

        if (! $product->delete()) {
            throw new RuntimeException('Le produit n’a pas pu être supprimé.');
        }

        PublicMedia::delete($image);

        return redirect()->route('admin.products.index')->with('success', 'Le produit a été supprimé.');
    }
}
