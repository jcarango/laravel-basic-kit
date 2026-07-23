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
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();
            // Referencia al Sufragante ya existente
            $table->foreignId('suffragan_id')->constrained('suffragans')->cascadeOnDelete();
            
            // Perfil
            $table->text('profile_summary')->nullable();
            
            // Scoring para ranking de especialistas o comités
            $table->integer('profile_score')->default(0)->index(); 
            
            // Disponibilidad estratégica
            $table->boolean('is_available_for_committees')->default(false)->index();
            
            // Campos de auditoría (Auditable Entity)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resumes');
    }
};
