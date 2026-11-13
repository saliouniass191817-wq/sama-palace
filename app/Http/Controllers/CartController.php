<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Support\PriceFormatter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    private const SESSION_KEY = 'whatsapp_cart';

    private const MAX_QUANTITY = 99;

    public function index(Request $request): View
    {
        return view('cart.index', $this->resolveCart($request));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $cart = $this->resolveCart($request);

        if ($cart['shop'] && $cart['shop']->id !== $product->shop_id) {
            return back()->withErrors([
                'cart' => 'Une commande ne peut contenir que des produits de la même boutique.',
            ]);
        }

        $quantity = ($cart['quantities'][$product->id] ?? 0) + 1;

        if ($quantity > self::MAX_QUANTITY) {
            return back()->withErrors([
                'cart' => 'La quantité maximale par produit est de '.self::MAX_QUANTITY.'.',
            ]);
        }

        $quantities = $cart['quantities'];
        $quantities[$product->id] = $quantity;

        $request->session()->put(self::SESSION_KEY, [
            'shop_id' => $product->shop_id,
            'items' => $quantities,
        ]);

        return back()->with('success', 'Produit ajouté au panier.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:'.self::MAX_QUANTITY],
        ]);
        $cart = $this->resolveCart($request);

        if (
            ! $cart['shop']
            || $cart['shop']->id !== $product->shop_id
            || ! array_key_exists($product->id, $cart['quantities'])
        ) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Ce produit ne se trouve pas dans votre panier.',
            ]);
        }

        $quantities = $cart['quantities'];
        $quantities[$product->id] = (int) $validated['quantity'];
        $request->session()->put(self::SESSION_KEY, [
            'shop_id' => $cart['shop']->id,
            'items' => $quantities,
        ]);

        return redirect()->route('cart.index')->with('success', 'Panier mis à jour.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $cart = $this->resolveCart($request);

        if (
            ! $cart['shop']
            || $cart['shop']->id !== $product->shop_id
            || ! array_key_exists($product->id, $cart['quantities'])
        ) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Ce produit ne se trouve pas dans votre panier.',
            ]);
        }

        $quantities = $cart['quantities'];
        unset($quantities[$product->id]);

        if ($quantities === []) {
            $request->session()->forget(self::SESSION_KEY);
        } else {
            $request->session()->put(self::SESSION_KEY, [
                'shop_id' => $cart['shop']->id,
                'items' => $quantities,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produit retiré du panier.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $cart = $this->resolveCart($request);

        if (! $cart['shop'] || $cart['items'] === []) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Votre panier est vide.',
            ]);
        }

        $shop = $cart['shop'];
        $shopUrl = route('shops.public', $shop->slug);
        $lines = [];

        foreach ($cart['items'] as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];
            $unitPrice = PriceFormatter::format($product->price);
            $subtotal = PriceFormatter::format($item['subtotal']);
            $productUrl = $shopUrl.'#product-'.$product->id;

            $lines[] = "- {$product->name} x {$quantity} - {$unitPrice} FCFA l'unité - {$subtotal} FCFA\n{$productUrl}";

            $product->clicks()->create([
                'shop_id' => $shop->id,
            ]);
        }

        $message = "Bonjour, je souhaite commander :\n\n"
            .implode("\n", $lines)
            ."\n\nTotal : ".PriceFormatter::format($cart['total'])." FCFA\nBoutique : {$shopUrl}";
        $phone = preg_replace('/\D+/', '', $shop->phone);

        return redirect()->away('https://wa.me/'.$phone.'?text='.rawurlencode($message));
    }

    /**
     * @return array{
     *     shop: ?Shop,
     *     quantities: array<int, int>,
     *     items: array<int, array{product: Product, quantity: int, subtotal: float}>,
     *     total: float
     * }
     */
    private function resolveCart(Request $request): array
    {
        $cart = $this->currentCart($request);

        if ($cart['shop_id'] === null || $cart['items'] === []) {
            $request->session()->forget(self::SESSION_KEY);

            return ['shop' => null, 'quantities' => [], 'items' => [], 'total' => 0];
        }

        $shop = Shop::find($cart['shop_id']);

        if (! $shop) {
            $request->session()->forget(self::SESSION_KEY);

            return ['shop' => null, 'quantities' => [], 'items' => [], 'total' => 0];
        }

        $products = $shop->products()
            ->whereKey(array_keys($cart['items']))
            ->get()
            ->keyBy('id');

        $quantities = [];
        $items = [];
        $total = 0.0;

        foreach ($cart['items'] as $productId => $quantity) {
            $product = $products->get($productId);

            if (! $product) {
                continue;
            }

            $subtotal = round((float) $product->price * $quantity, 2);
            $quantities[$product->id] = $quantity;
            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
            $total += $subtotal;
        }

        if ($quantities === []) {
            $request->session()->forget(self::SESSION_KEY);

            return ['shop' => null, 'quantities' => [], 'items' => [], 'total' => 0];
        }

        $request->session()->put(self::SESSION_KEY, [
            'shop_id' => $shop->id,
            'items' => $quantities,
        ]);

        return [
            'shop' => $shop,
            'quantities' => $quantities,
            'items' => $items,
            'total' => round($total, 2),
        ];
    }

    /**
     * @return array{shop_id: ?int, items: array<int, int>}
     */
    private function currentCart(Request $request): array
    {
        $cart = $request->session()->get(self::SESSION_KEY, []);

        if (! is_array($cart) || ! is_array($cart['items'] ?? null)) {
            return ['shop_id' => null, 'items' => []];
        }

        $shopId = filter_var($cart['shop_id'] ?? null, FILTER_VALIDATE_INT);

        if (! is_int($shopId) || $shopId < 1) {
            return ['shop_id' => null, 'items' => []];
        }

        $items = [];

        foreach ($cart['items'] as $productId => $quantity) {
            $productId = filter_var($productId, FILTER_VALIDATE_INT);
            $quantity = filter_var($quantity, FILTER_VALIDATE_INT);

            if (
                is_int($productId) && $productId > 0
                && is_int($quantity) && $quantity > 0 && $quantity <= self::MAX_QUANTITY
            ) {
                $items[$productId] = $quantity;
            }
        }

        return ['shop_id' => $shopId, 'items' => $items];
    }
}
