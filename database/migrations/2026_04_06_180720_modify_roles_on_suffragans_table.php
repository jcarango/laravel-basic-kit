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
        Schema::table('suffragans', function (Blueprint $table) {
            if (Schema::hasColumn('suffragans', 'role_type')) {
                $table->dropColumn('role_type');
            }
            $table->boolean('is_leader')->default(false);
            $table->boolean('is_witness')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suffragans', function (Blueprint $table) {
            $table->dropColumn(['is_leader', 'is_witness']);
            $table->string('role_type')->nullable()->default('Sufragante');
        });
    }
};
