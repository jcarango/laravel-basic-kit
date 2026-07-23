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
        if (!Schema::hasColumn('estrategias', 'sentiment')) {
            Schema::table('estrategias', function (Blueprint $table) {
                $table->string('sentiment')->nullable();
                $table->integer('positive_score')->nullable();
                $table->integer('negative_score')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estrategias', function (Blueprint $table) {
            //
        });
    }
};
