<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('adress', 'address');
            $table->renameColumn('firstName', 'first_name');
            $table->renameColumn('lastName', 'last_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('address', 'adress');
            $table->renameColumn('first_name', 'firstName');
            $table->renameColumn('last_name', 'lastName');
        });
    }
};
