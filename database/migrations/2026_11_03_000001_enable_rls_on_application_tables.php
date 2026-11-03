<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = [
        'users',
        'password_reset_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'shops',
        'products',
        'clicks',
        'migrations',
    ];

    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        foreach (self::TABLES as $table) {
            if (Schema::hasTable($table)) {
                DB::statement(sprintf('ALTER TABLE public."%s" ENABLE ROW LEVEL SECURITY', $table));
            }
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        foreach (self::TABLES as $table) {
            if (Schema::hasTable($table)) {
                DB::statement(sprintf('ALTER TABLE public."%s" DISABLE ROW LEVEL SECURITY', $table));
            }
        }
    }
};
