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
        Schema::create('suffragan_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suffragan_id')->constrained('suffragans')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            
            $table->enum('level', ['Básico', 'Intermedio', 'Avanzado', 'Experto']);
            $table->unsignedTinyInteger('years_experience')->default(0);
            
            $table->timestamps();
            
            $table->unique(['suffragan_id', 'skill_id'], 'uk_suffragan_skill');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suffragan_skills');
    }
};
