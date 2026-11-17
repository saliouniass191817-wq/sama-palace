<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Http\Request;

class ClickRecorder
{
    public function record(Request $request, Product $product): void
    {
        if (RequestAutomationDetector::isAutomated($request)) {
            return;
        }

        $ipAddress = $request->ip() ?? $request->server('REMOTE_ADDR');

        $product->clicks()->create([
            'shop_id' => $product->shop_id,
            'ip_address' => is_string($ipAddress) && $ipAddress !== ''
                ? substr(hash_hmac('sha256', $ipAddress, (string) config('app.key')), 0, 40)
                : null,
        ]);
    }
}
