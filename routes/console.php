<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sessions:prune-expired', function () {
    $cutoff = now()->subMinutes((int) config('session.lifetime'))->getTimestamp();
    $deleted = DB::table(config('session.table'))
        ->where('last_activity', '<', $cutoff)
        ->delete();

    $this->info("Pruned {$deleted} expired sessions.");
})->purpose('Delete expired database sessions');

Schedule::command('sessions:prune-expired')->daily();
