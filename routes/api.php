<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/readyz', function () {
    try {
        DB::select('select 1');

        return response('ready', 200);
    } catch (\Throwable) {
        return response('unavailable', 503);
    }
})->name('health.ready');
