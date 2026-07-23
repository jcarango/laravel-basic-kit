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
        Schema::create('academic_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suffragan_id')->constrained('suffragans')->cascadeOnDelete();
            
            $table->string('institution');
            $table->string('degree');
            $table->enum('status', ['En curso', 'Graduado', 'Abandonado'])->default('Graduado');
            
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('currently_studying')->default(false);
            $table->boolean('is_verified')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_backgrounds');
    }
};
