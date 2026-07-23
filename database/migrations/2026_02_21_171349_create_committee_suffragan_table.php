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
        Schema::create('committee_suffragan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suffragan_id')->constrained('suffragans')->cascadeOnDelete();
            $table->foreignId('committee_id')->constrained('political_committees')->cascadeOnDelete();
            
            $table->string('role', 50)->default('Miembro'); // Coordinador, Enlace
            $table->date('joined_at')->useCurrent();
            
            $table->timestamps();
            
            $table->unique(['suffragan_id', 'committee_id'], 'uk_suffragan_committee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_suffragan');
    }
};
