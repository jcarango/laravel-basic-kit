<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('habeas_data_accepted')->default(false);
        });

        Schema::table('suffragans', function (Blueprint $table) {
            $table->boolean('habeas_data_accepted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('habeas_data_accepted');
        });

        Schema::table('suffragans', function (Blueprint $table) {
            $table->dropColumn('habeas_data_accepted');
        });
    }
};
