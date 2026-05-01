<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WhatsAppOrderController extends Controller
{
    public function __invoke(Request $request, Product $product): RedirectResponse
    {
        $product->load('shop');
        $shop = $product->shop;

        $product->clicks()->create([
            'shop_id' => $shop->id,
            'ip_address' => $request->ip(),
        ]);

        $phone = preg_replace('/\D+/', '', $shop->phone);
        $productUrl = route('shops.public', $shop->slug) . '#product-' . $product->id;
        $price = number_format((float) $product->price, 0, ',', ' ') . ' FCFA';
        $message = "Hello, I want to order: {$product->name} - Price: {$price}. Link: {$productUrl}";

        return redirect()->away('https://wa.me/' . $phone . '?text=' . rawurlencode($message));
    }
}
