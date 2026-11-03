<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('phone');
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement(<<<'SQL'
                DO $$
                DECLARE citext_schema text;
                BEGIN
                    SELECT n.nspname INTO citext_schema
                    FROM pg_extension e
                    JOIN pg_namespace n ON n.oid = e.extnamespace
                    WHERE e.extname = 'citext';

                    EXECUTE format('ALTER TABLE shops ALTER COLUMN slug TYPE %I.citext', citext_schema);
                END $$
                SQL);
            DB::statement('ALTER TABLE shops ADD CONSTRAINT shops_slug_length_check CHECK (char_length(slug) <= 255)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
