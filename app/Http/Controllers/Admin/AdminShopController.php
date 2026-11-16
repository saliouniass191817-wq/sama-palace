<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageShopRequest;
use App\Models\Shop;
use App\Models\User;
use App\Support\PublicMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class AdminShopController extends Controller
{
    public function index(): View
    {
        return view('admin.shops.index', [
            'shops' => Shop::with('user')->withCount('products')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.shops.create', ['users' => User::orderBy('firstName')->get()]);
    }

    public function store(ManageShopRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $logo = $request->file('logo');
        unset($validated['logo']);

        if ($logo) {
            PublicMedia::storeAndPersist(
                $logo,
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

        return redirect()->route('admin.shops.index')->with('success', 'La boutique a été créée.');
    }

    public function edit(Shop $shop): View
    {
        return view('admin.shops.edit', [
            'shop' => $shop,
            'users' => User::orderBy('firstName')->get(),
        ]);
    }

    public function update(ManageShopRequest $request, Shop $shop): RedirectResponse
    {
        $validated = $request->validated();
        $logo = $request->file('logo');
        unset($validated['logo']);

        if ($logo) {
            PublicMedia::storeAndPersist(
                $logo,
                'shops',
                'logo',
                function (string $path) use ($shop, $validated) {
                    $validated['logo'] = $path;

                    return $shop->update($validated);
                },
                $shop->logo,
            );
        } elseif (! $shop->update($validated)) {
            throw new RuntimeException('La boutique n’a pas pu être mise à jour.');
        }

        return redirect()->route('admin.shops.index')->with('success', 'La boutique a été mise à jour.');
    }

    public function destroy(Shop $shop): RedirectResponse
    {
        $productImages = $shop->products()->pluck('image');
        $logo = $shop->logo;

        if (! $shop->delete()) {
            throw new RuntimeException('La boutique n’a pas pu être supprimée.');
        }

        foreach ($productImages as $image) {
            PublicMedia::delete($image);
        }

        PublicMedia::delete($logo);

        return redirect()->route('admin.shops.index')->with('success', 'La boutique a été supprimée.');
    }
}
