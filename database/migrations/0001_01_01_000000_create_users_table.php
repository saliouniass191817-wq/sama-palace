<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstName');
            $table->string('lastName');
            $table->string('phone')->unique();
            $table->string('adress');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('phone')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS citext WITH SCHEMA extensions');
            DB::statement(<<<'SQL'
                DO $$
                DECLARE citext_schema text;
                BEGIN
                    SELECT n.nspname INTO citext_schema
                    FROM pg_extension e
                    JOIN pg_namespace n ON n.oid = e.extnamespace
                    WHERE e.extname = 'citext';

                    EXECUTE format('ALTER TABLE users ALTER COLUMN phone TYPE %I.citext', citext_schema);
                END $$
                SQL);
            DB::statement('ALTER TABLE users ADD CONSTRAINT users_phone_length_check CHECK (char_length(phone) <= 255)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
