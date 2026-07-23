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
        Schema::create('e14conteos', function (Blueprint $table) {
            $table->id();
            $table->string('mesa');
            $table->string('photo')->nullable();
            $table->string('total_sufragantes_e11')->nullable();
            $table->string('total_votos_urna')->nullable();
            $table->string('total_votos_incinerados')->nullable();
            $table->string('votos_nulos')->nullable();
            $table->string('votos_no_marcados')->nullable();
            $table->string('total_votos_mesa')->nullable();
            $table->boolean('hubo_reconteo')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e14conteos');
    }
};
