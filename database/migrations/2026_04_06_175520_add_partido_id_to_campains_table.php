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
        Schema::table('campains', function (Blueprint $table) {
            $table->foreignId('partido_id')->nullable()->constrained('partidos')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campains', function (Blueprint $table) {
            $table->dropForeign(['partido_id']);
            $table->dropColumn('partido_id');
        });
    }
};
