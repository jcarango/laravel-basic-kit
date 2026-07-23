<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estrategias', function (Blueprint $table) {
            $table->text('analisis')->nullable()->after('situacionlograble');
            $table->json('analisis_detallado')->nullable()->after('analisis');
            $table->string('analisis_status')->default('pendiente')->after('analisis_detallado');
        });
    }

    public function down(): void
    {
        Schema::table('estrategias', function (Blueprint $table) {
            $table->dropColumn(['analisis', 'analisis_detallado', 'analisis_status']);
        });
    }
};