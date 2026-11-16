<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\PriceFormatter;
use App\Support\RequestAutomationDetector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WhatsAppOrderController extends Controller
{
    public function __invoke(Request $request, Product $product): RedirectResponse
    {
        $product->load('shop');
        $shop = $product->shop;

        if (! RequestAutomationDetector::isAutomated($request)) {
            $ipAddress = $request->ip() ?? $request->server('REMOTE_ADDR');

            $product->clicks()->create([
                'shop_id' => $shop->id,
                'ip_address' => is_string($ipAddress) && $ipAddress !== ''
                    ? substr(hash_hmac('sha256', $ipAddress, (string) config('app.key')), 0, 40)
                    : null,
            ]);
        }

        $phone = preg_replace('/\D+/', '', $shop->phone);
        $productUrl = route('shops.public', $shop->slug).'#product-'.$product->id;
        $price = PriceFormatter::format($product->price).' FCFA';
        $message = "Bonjour, je souhaite commander : {$product->name} - Prix : {$price}. Lien : {$productUrl}";

        return redirect()->away('https://wa.me/'.$phone.'?text='.rawurlencode($message));
    }

}
