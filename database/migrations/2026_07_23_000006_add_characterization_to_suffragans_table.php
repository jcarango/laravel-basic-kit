<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suffragans', function (Blueprint $table) {
            if (!Schema::hasColumn('suffragans', 'consecutivo')) {
                $table->string('consecutivo')->nullable();
            }
            if (!Schema::hasColumn('suffragans', 'characterization_date')) {
                $table->date('characterization_date')->nullable();
            }
            if (!Schema::hasColumn('suffragans', 'vereda')) {
                $table->string('vereda')->nullable();
            }
            if (!Schema::hasColumn('suffragans', 'corregimiento')) {
                $table->string('corregimiento')->nullable();
            }

            // Información del predio
            if (!Schema::hasColumn('suffragans', 'property_name')) {
                $table->string('property_name')->nullable();
            }
            if (!Schema::hasColumn('suffragans', 'total_area')) {
                $table->decimal('total_area', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('suffragans', 'available_area')) {
                $table->decimal('available_area', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('suffragans', 'cadastral_status')) {
                $table->string('cadastral_status')->nullable(); // Escritura pública, Compraventa, Sucesión, Otro
            }

            // Proyectos y Discapacidad
            if (!Schema::hasColumn('suffragans', 'is_project_beneficiary')) {
                $table->boolean('is_project_beneficiary')->default(false);
            }
            if (!Schema::hasColumn('suffragans', 'project_name')) {
                $table->string('project_name')->nullable();
            }
            if (!Schema::hasColumn('suffragans', 'has_disability')) {
                $table->boolean('has_disability')->default(false);
            }
            if (!Schema::hasColumn('suffragans', 'disability_type')) {
                $table->string('disability_type')->nullable();
            }
            if (!Schema::hasColumn('suffragans', 'gender')) {
                $table->string('gender')->nullable();
            }

            // Línea productiva
            if (!Schema::hasColumn('suffragans', 'livestock_count')) {
                $table->integer('livestock_count')->nullable();
            }
            if (!Schema::hasColumn('suffragans', 'species')) {
                $table->string('species')->nullable();
            }
            if (!Schema::hasColumn('suffragans', 'unit_of_measure')) {
                $table->string('unit_of_measure')->nullable();
            }

            // Grupos poblacionales
            if (!Schema::hasColumn('suffragans', 'population_groups')) {
                $table->json('population_groups')->nullable();
            }

            // Asociaciones
            if (!Schema::hasColumn('suffragans', 'belongs_to_association')) {
                $table->boolean('belongs_to_association')->default(false);
            }
            if (!Schema::hasColumn('suffragans', 'association_name')) {
                $table->string('association_name')->nullable();
            }

            // Proyecto Corderos
            if (!Schema::hasColumn('suffragans', 'knows_lamb_project')) {
                $table->boolean('knows_lamb_project')->default(false);
            }
            if (!Schema::hasColumn('suffragans', 'lamb_project_source')) {
                $table->string('lamb_project_source')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('suffragans', function (Blueprint $table) {
            $table->dropColumn([
                'consecutivo', 'characterization_date', 'vereda', 'corregimiento',
                'property_name', 'total_area', 'available_area', 'cadastral_status',
                'is_project_beneficiary', 'project_name', 'has_disability', 'disability_type', 'gender',
                'livestock_count', 'species', 'unit_of_measure',
                'population_groups', 'belongs_to_association', 'association_name',
                'knows_lamb_project', 'lamb_project_source'
            ]);
        });
    }
};
