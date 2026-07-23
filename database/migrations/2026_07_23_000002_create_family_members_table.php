<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suffragan_id')->constrained('suffragans')->onDelete('cascade');
            $table->string('name');
            $table->string('document_number')->nullable();
            $table->string('relationship'); // Esposo(a), Hijo(a), Padre/Madre, Hermano(a), Otro
            $table->integer('age')->nullable();
            $table->string('gender')->nullable(); // Masculino, Femenino, Otro
            $table->string('phone')->nullable();
            $table->string('occupation')->nullable();
            $table->string('education_level')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
