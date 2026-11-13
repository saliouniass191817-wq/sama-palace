<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\PriceFormatter;
use Illuminate\Http\RedirectResponse;

class WhatsAppOrderController extends Controller
{
    public function __invoke(Product $product): RedirectResponse
    {
        $product->load('shop');
        $shop = $product->shop;

        $product->clicks()->create([
            'shop_id' => $shop->id,
        ]);

        $phone = preg_replace('/\D+/', '', $shop->phone);
        $productUrl = route('shops.public', $shop->slug) . '#product-' . $product->id;
        $price = PriceFormatter::format($product->price).' FCFA';
        $message = "Hello, I want to order: {$product->name} - Price: {$price}. Link: {$productUrl}";

        return redirect()->away('https://wa.me/' . $phone . '?text=' . rawurlencode($message));
    }
}
