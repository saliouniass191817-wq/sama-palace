<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function dashboard(Request $request): View
    {
        $shop = $request->user()
            ->shop()
            ->with(['products' => fn ($query) => $query->withCount('clicks')->latest()])
            ->withCount('clicks')
            ->first();

        $mostClickedProduct = $shop?->products
            ->sortByDesc('clicks_count')
            ->firstWhere('clicks_count', '>', 0);

        return view('shops.dashboard', compact('shop', 'mostClickedProduct'));
    }

    public function create(Request $request): RedirectResponse|View
    {
        if ($request->user()->shop) {
            return redirect()->route('shops.edit')->with('success', 'You already have a shop.');
        }

        return view('shops.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->shop) {
            return redirect()->route('shops.edit');
        }

        $validated = $this->validateShop($request);
        $validated['user_id'] = $request->user()->id;
        $validated['slug'] = $this->uniqueSlug($validated['name']);

        if ($request->hasFile('logo')) {
            $logoUrl = Cloudinary::upload($request->file('logo')->getRealPath(), [
                'folder' => 'shops'
            ])->getSecurePath();
            $validated['logo'] = $logoUrl;
        }

        Shop::create($validated);

        return redirect()->route('dashboard')->with('success', 'Shop created successfully.');
    }

    public function edit(Request $request): View
    {
        $shop = $request->user()->shop;
        abort_if(! $shop, 404);

        return view('shops.edit', compact('shop'));
    }

    public function update(Request $request): RedirectResponse
    {
        $shop = $request->user()->shop;
        abort_if(! $shop, 404);

        $validated = $this->validateShop($request, $shop);
        $validated['slug'] = $this->uniqueSlug($validated['name'], $shop);

        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo Cloudinary si existe
            if ($shop->logo && str_contains($shop->logo, 'cloudinary.com')) {
                $publicId = $this->extractCloudinaryPublicId($shop->logo);
                if ($publicId) {
                    Cloudinary::destroy($publicId);
                }
            }

            $logoUrl = Cloudinary::upload($request->file('logo')->getRealPath(), [
                'folder' => 'shops'
            ])->getSecurePath();
            $validated['logo'] = $logoUrl;
        }

        $shop->update($validated);

        return redirect()->route('dashboard')->with('success', 'Shop updated successfully.');
    }

    public function show(string $slug): View
    {
        $shop = Shop::with('products')->where('slug', $slug)->firstOrFail();

        return view('shops.show', compact('shop'));
    }

    private function validateShop(Request $request, ?Shop $shop = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['required', 'string', 'max:30'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function uniqueSlug(string $name, ?Shop $shop = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (
            Shop::where('slug', $slug)
                ->when($shop, fn ($query) => $query->whereKeyNot($shop->id))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
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
