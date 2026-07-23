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
        Schema::create('divipols', function (Blueprint $table) {
            $table->id();
            $table->string('dep');
            $table->string('mun');
            $table->string('zon');
            $table->string('pto');
            $table->string('departamento');
            $table->string('municipio');
            $table->string('nom_puesto');
            $table->string('direccion');
            $table->string('ind_mesa');
            $table->string('categoria');
            $table->string('mujeres');
            $table->string('hombres');
            $table->string('potencial');
            $table->string('mesas_totales');
            $table->string('jal');
            $table->string('nom_jal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divipols');
    }
};
