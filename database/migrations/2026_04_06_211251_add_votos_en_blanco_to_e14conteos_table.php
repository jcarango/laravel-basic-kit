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
            $table->string('votos_en_blanco')->nullable()->after('total_votos_incinerados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('e14conteos', function (Blueprint $table) {
            $table->dropColumn('votos_en_blanco');
        });
    }
};
