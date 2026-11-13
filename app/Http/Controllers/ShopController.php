<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveShopRequest;
use App\Models\Shop;
use App\Support\PublicMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class ShopController extends Controller
{
    public function dashboard(Request $request): View
    {
        $shop = $request->user()
            ->shop()
            ->withCount(['products', 'clicks'])
            ->first();

        $products = collect();
        $mostClickedProduct = null;

        if ($shop) {
            $products = $shop->products()
                ->withCount('clicks')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(12);

            $mostClickedProduct = $shop->products()
                ->withCount('clicks')
                ->orderByDesc('clicks_count')
                ->orderByDesc('id')
                ->first();

            if ($mostClickedProduct && $mostClickedProduct->clicks_count < 1) {
                $mostClickedProduct = null;
            }
        }

        return view('shops.dashboard', compact('shop', 'products', 'mostClickedProduct'));
    }

    public function create(Request $request): RedirectResponse|View
    {
        if ($request->user()->shop) {
            return redirect()->route('shops.edit')->with('success', 'You already have a shop.');
        }

        return view('shops.create');
    }

    public function store(SaveShopRequest $request): RedirectResponse
    {
        if ($request->user()->shop) {
            return redirect()->route('shops.edit');
        }

        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;
        $validated['slug'] = $this->uniqueSlug($validated['name']);

        if ($request->hasFile('logo')) {
            PublicMedia::storeAndPersist(
                $request->file('logo'),
                'shops',
                'logo',
                function (string $path) use ($validated) {
                    $validated['logo'] = $path;

                    return Shop::create($validated);
                },
            );
        } else {
            Shop::create($validated);
        }

        return redirect()->route('dashboard')->with('success', 'Shop created successfully.');
    }

    public function edit(Request $request): View
    {
        $shop = $request->user()->shop;
        abort_if(! $shop, 404);

        return view('shops.edit', compact('shop'));
    }

    public function update(SaveShopRequest $request): RedirectResponse
    {
        $shop = $request->user()->shop;
        abort_if(! $shop, 404);

        $validated = $request->validated();
        $validated['slug'] = $this->uniqueSlug($validated['name'], $shop);

        if ($request->hasFile('logo')) {
            PublicMedia::storeAndPersist(
                $request->file('logo'),
                'shops',
                'logo',
                function (string $path) use ($shop, $validated) {
                    $validated['logo'] = $path;

                    return $shop->update($validated);
                },
                $shop->logo,
            );
        } elseif (! $shop->update($validated)) {
            throw new RuntimeException('The shop could not be updated.');
        }

        return redirect()->route('dashboard')->with('success', 'Shop updated successfully.');
    }

    public function show(string $slug): View
    {
        $shop = Shop::where('slug', $slug)->firstOrFail();
        $products = $shop->products()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(12);

        return view('shops.show', compact('shop', 'products'));
    }

    private function uniqueSlug(string $name, ?Shop $shop = null): string
    {
        $baseSlug = Str::slug($name) ?: 'shop';
        $slug = $baseSlug;
        $counter = 2;

        while (
            Shop::where('slug', $slug)
                ->when($shop, fn ($query) => $query->whereKeyNot($shop->id))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
