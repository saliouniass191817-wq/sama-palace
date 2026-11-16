<?php

namespace App\Support;

use Illuminate\Http\Request;

class RequestAutomationDetector
{
    public static function isAutomated(Request $request): bool
    {
        $userAgent = $request->userAgent() ?? '';

        if (preg_match('/bot|crawler|spider|slurp|preview/i', $userAgent)) {
            return true;
        }

        return preg_match('/prefetch|prerender/i', implode(' ', [
            $request->header('Purpose', ''),
            $request->header('Sec-Purpose', ''),
        ])) === 1;
    }
}
