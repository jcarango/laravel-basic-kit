<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (!Schema::hasColumn('candidates', 'cargo_aspira')) {
                $table->string('cargo_aspira')->nullable()->after('partido_id');
            }
            if (!Schema::hasColumn('candidates', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('cargo_aspira');
            }
            if (!Schema::hasColumn('candidates', 'is_principal')) {
                $table->boolean('is_principal')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('candidates', 'is_opponent')) {
                $table->boolean('is_opponent')->default(false)->after('is_principal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn(['cargo_aspira', 'is_active', 'is_principal', 'is_opponent']);
        });
    }
};
