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
        Schema::table('e14conteos', function (Blueprint $table) {
            $table->foreignId('divipol_id')->nullable()->constrained('divipols')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('e14conteos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('divipol_id');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
