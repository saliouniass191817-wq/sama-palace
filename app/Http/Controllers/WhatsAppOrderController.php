<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\ClickRecorder;
use App\Support\PriceFormatter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WhatsAppOrderController extends Controller
{
    public function __invoke(Request $request, Product $product, ClickRecorder $clickRecorder): RedirectResponse
    {
        $product->load('shop');
        $shop = $product->shop;
        $clickRecorder->record($request, $product);

        $phone = preg_replace('/\D+/', '', $shop->phone);
        $productUrl = route('shops.public', $shop->slug).'#product-'.$product->id;
        $price = PriceFormatter::format($product->price).' FCFA';
        $message = "Bonjour, je souhaite commander : {$product->name} - Prix : {$price}. Lien : {$productUrl}";

        return redirect()->away('https://wa.me/'.$phone.'?text='.rawurlencode($message));
    }
}
