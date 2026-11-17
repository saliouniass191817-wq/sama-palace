<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveProductRequest;
use App\Models\Product;
use App\Support\PublicMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use RuntimeException;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $shop = $this->sellerShop($request);
        $products = $shop->products()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(12);

        return view('products.index', compact('shop', 'products'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Product::class);

        return view('products.create');
    }

    public function store(SaveProductRequest $request): RedirectResponse
    {
        $shop = $request->user()->shop;
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            PublicMedia::storeAndPersist(
                $request->file('image'),
                'products',
                'image',
                function (string $path) use ($shop, $validated) {
                    $validated['image'] = $path;

                    return $shop->products()->create($validated);
                },
            );
        } else {
            $shop->products()->create($validated);
        }

        return redirect()
            ->route('products.index')
            ->with('success', __('messages.product.created'));
    }

    public function edit(Request $request, Product $product): View
    {
        Gate::authorize('update', $product);

        return view('products.edit', compact('product'));
    }

    public function update(SaveProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            PublicMedia::storeAndPersist(
                $request->file('image'),
                'products',
                'image',
                function (string $path) use ($product, $validated) {
                    $validated['image'] = $path;

                    return $product->update($validated);
                },
                $product->image,
            );
        } elseif (! $product->update($validated)) {
            throw new RuntimeException(__('messages.product.update_failed'));
        }

        return redirect()
            ->route('products.index')
            ->with('success', __('messages.product.updated'));
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);

        if (! $product->delete()) {
            throw new RuntimeException(__('messages.product.delete_failed'));
        }

        PublicMedia::delete($product->image);

        return redirect()
            ->route('products.index')
            ->with('success', __('messages.product.deleted'));
    }

    private function sellerShop(Request $request)
    {
        $shop = $request->user()->shop;

        abort_if(! $shop, 403, __('messages.shop.create_first'));

        return $shop;
    }
}
