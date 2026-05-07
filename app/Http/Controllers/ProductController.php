<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $shop = $this->sellerShop($request);
        $products = $shop->products()->latest()->get();

        return view('products.index', compact('shop', 'products'));
    }

    public function create(Request $request): View
    {
        $this->sellerShop($request);

        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $shop = $this->sellerShop($request);

        $validated = $this->validateProduct($request);

        if ($request->hasFile('image')) {
            $path = Storage::disk('s3')->put('products', fopen($request->file('image')->getRealPath(), 'r'));
            $validated['image'] = $this->supabasePublicUrl($path);
        }

        $shop->products()->create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Request $request, Product $product): View
    {
        $this->authorizeProduct($request, $product);

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeProduct($request, $product);

        $validated = $this->validateProduct($request);

        if ($request->hasFile('image')) {
            if ($product->image) {
                $oldPath = $this->extractSupabasePath($product->image);
                if ($oldPath) {
                    Storage::disk('s3')->delete($oldPath);
                }
            }

            $path = Storage::disk('s3')->put('products', fopen($request->file('image')->getRealPath(), 'r'));
            $validated['image'] = $this->supabasePublicUrl($path);
        }

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeProduct($request, $product);

        if ($product->image) {
            $oldPath = $this->extractSupabasePath($product->image);
            if ($oldPath) {
                Storage::disk('s3')->delete($oldPath);
            }
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function sellerShop(Request $request)
    {
        $shop = $request->user()->shop;

        abort_if(! $shop, 403, 'Create your shop before adding products.');

        return $shop;
    }

    private function authorizeProduct(Request $request, Product $product): void
    {
        abort_if(
            $product->shop_id !== $this->sellerShop($request)->id,
            403
        );
    }

    private function extractSupabasePath(string $url): ?string
    {
        $endpoint = rtrim(str_replace('/storage/v1', '', env('AWS_ENDPOINT')), '/');
        $prefix = $endpoint . '/storage/v1/object/public/' . env('AWS_BUCKET') . '/';

        if (str_starts_with($url, $prefix)) {
            return substr($url, strlen($prefix));
        }

        return null;
    }

    private function supabasePublicUrl(string $path): string
    {
        $endpoint = rtrim(str_replace('/storage/v1', '', env('AWS_ENDPOINT')), '/');

        return $endpoint . '/storage/v1/object/public/' . env('AWS_BUCKET') . '/' . ltrim($path, '/');
    }
}
