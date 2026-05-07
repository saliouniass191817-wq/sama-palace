<?php

namespace App\Http\Controllers;

use App\Models\Product;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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
            $imageUrl = Cloudinary::upload($request->file('image')->getRealPath(), [
                'folder' => 'produits'
            ])->getSecurePath();
            $validated['image'] = $imageUrl;
        }

        $shop->products()->create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
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
            // Supprimer l'ancienne image Cloudinary si elle existe
            if ($product->image && str_contains($product->image, 'cloudinary.com')) {
                $publicId = $this->extractCloudinaryPublicId($product->image);
                if ($publicId) {
                    Cloudinary::destroy($publicId);
                }
            }

            $imageUrl = Cloudinary::upload($request->file('image')->getRealPath(), [
                'folder' => 'produits'
            ])->getSecurePath();
            $validated['image'] = $imageUrl;
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeProduct($request, $product);

        if ($product->image) {
            if (str_contains($product->image, 'cloudinary.com')) {
                $publicId = $this->extractCloudinaryPublicId($product->image);
                if ($publicId) {
                    Cloudinary::destroy($publicId);
                }
            } else {
                Storage::disk('public')->delete($product->image);
            }
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
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
        abort_if($product->shop_id !== $this->sellerShop($request)->id, 403);
    }

    private function extractCloudinaryPublicId(string $url): ?string
    {
        // URL format: https://res.cloudinary.com/{cloud_name}/image/upload/{transformations}/{public_id}
        $pattern = '/\/upload\/(?:v\d+\/)?(.+)\.[a-zA-Z]+$/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
