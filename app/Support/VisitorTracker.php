<?php

namespace App\Support;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitorTracker
{
    public function record(Request $request, ?Shop $shop = null): void
    {
        if ($request->user() || RequestAutomationDetector::isAutomated($request)) {
            return;
        }

        $ipAddress = $request->ip() ?? $request->server('REMOTE_ADDR');

        if (! is_string($ipAddress) || $ipAddress === '') {
            return;
        }

        $now = now();
        $date = $now->toDateString();
        $visitorHash = hash_hmac('sha256', $date.'|'.$ipAddress, (string) config('app.key'));
        $scopes = [
            ['scope_key' => 'site', 'shop_id' => null],
        ];

        if ($shop) {
            $scopes[] = ['scope_key' => 'shop:'.$shop->id, 'shop_id' => $shop->id];
        }

        $records = array_map(
            fn (array $scope): array => [
                'visitor_hash' => $visitorHash,
                'visited_on' => $date,
                'scope_key' => $scope['scope_key'],
                'shop_id' => $scope['shop_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $scopes,
        );

        DB::table('visitor_visits')->insertOrIgnore($records);
    }
}
