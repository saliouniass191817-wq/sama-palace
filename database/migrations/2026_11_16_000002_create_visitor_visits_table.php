<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_hash', 64);
            $table->date('visited_on');
            $table->string('scope_key', 255);
            $table->foreignId('shop_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['visitor_hash', 'visited_on', 'scope_key']);
            $table->index(['scope_key', 'visited_on']);
        });

        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE public."visitor_visits" ENABLE ROW LEVEL SECURITY');
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql' && Schema::hasTable('visitor_visits')) {
            DB::statement('ALTER TABLE public."visitor_visits" DISABLE ROW LEVEL SECURITY');
        }

        Schema::dropIfExists('visitor_visits');
    }
};
