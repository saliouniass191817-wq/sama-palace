<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/readyz', function () {
    try {
        DB::select('select 1');

        $migrator = app('migrator');

        if (! $migrator->repositoryExists()) {
            return response('unavailable', 503);
        }

        $migrationPaths = array_merge([database_path('migrations')], $migrator->paths());
        $migrationNames = array_keys($migrator->getMigrationFiles($migrationPaths));
        $ranMigrations = $migrator->getRepository()->getRan();

        if (array_diff($migrationNames, $ranMigrations) !== []) {
            return response('unavailable', 503);
        }

        return response('ready', 200);
    } catch (\Throwable) {
        return response('unavailable', 503);
    }
})->name('health.ready');
