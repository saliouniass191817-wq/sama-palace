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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->smallInteger('attempts');
            $table->bigInteger('reserved_at')->nullable();
            $table->bigInteger('available_at');
            $table->bigInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE jobs ADD CONSTRAINT jobs_attempts_unsigned_range CHECK (attempts BETWEEN 0 AND 255)');
            DB::statement('ALTER TABLE jobs ADD CONSTRAINT jobs_reserved_at_unsigned_range CHECK (reserved_at IS NULL OR reserved_at BETWEEN 0 AND 4294967295)');
            DB::statement('ALTER TABLE jobs ADD CONSTRAINT jobs_available_at_unsigned_range CHECK (available_at BETWEEN 0 AND 4294967295)');
            DB::statement('ALTER TABLE jobs ADD CONSTRAINT jobs_created_at_unsigned_range CHECK (created_at BETWEEN 0 AND 4294967295)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
