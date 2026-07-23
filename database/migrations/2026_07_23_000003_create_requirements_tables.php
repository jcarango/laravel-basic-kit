<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['particular', 'colectivo']);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('requirement_suffragan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suffragan_id')->constrained('suffragans')->onDelete('cascade');
            $table->foreignId('requirement_id')->constrained('requirements')->onDelete('cascade');
            $table->string('status')->default('Pendiente'); // Pendiente, En Proceso, Resuelto
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requirement_suffragan');
        Schema::dropIfExists('requirements');
    }
};
