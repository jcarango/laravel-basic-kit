<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estrategias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->text('quiereser')->nullable();
            $table->text('determinoimagen')->nullable();
            $table->text('identificoproblemas')->nullable();
            $table->text('identificoseguidores')->nullable();
            $table->text('identificocapacidad')->nullable();
            $table->text('iteresproyecto')->nullable();
            $table->text('mejorqueotros')->nullable();
            $table->text('Propuesta')->nullable();
            $table->text('Sectorpriorizado')->nullable();
            $table->text('problematicadeterminada')->nullable();
            $table->text('objetivogeneral')->nullable();
            $table->text('objetivosestrategicos')->nullable();
            $table->text('planeacionestrategia')->nullable();
            $table->text('plandesarrollo')->nullable();
            $table->text('planproceso')->nullable();
            $table->text('planmejoramiento')->nullable();
            $table->text('situacionreal')->nullable();
            $table->text('insumos')->nullable();
            $table->text('procesos')->nullable();
            $table->text('productos')->nullable();
            $table->text('resultados')->nullable();
            $table->text('impactos')->nullable();
            $table->text('situacionlograble')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estrategias');
    }
};
