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
        Schema::create('leader_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leader_id')->constrained('suffragans')->cascadeOnDelete();
            $table->string('type'); // Especie, Dinero, Contrato, Obra, Ayuda, Bono, Otro
            $table->string('concept');
            $table->integer('quantity')->default(1);
            $table->decimal('value', 15, 2)->nullable();
            $table->string('status')->default('Solicitado'); // Solicitado, Aprobado, En Proceso, Entregado, Rechazado
            $table->date('delivery_date')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('responsible_person')->nullable();
            $table->text('description')->nullable();
            $table->string('attachment_path')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leader_resources');
    }
};
