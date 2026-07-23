<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            if (!Schema::hasColumn('survey_responses', 'document_number')) {
                $table->string('document_number')->nullable()->after('respondent_name');
                $table->string('phone')->nullable()->after('document_number');
                $table->string('email')->nullable()->after('phone');
                $table->string('address')->nullable()->after('email');
                $table->foreignId('city_id')->nullable()->after('address')->constrained('cities')->nullOnDelete();
                $table->string('latitude')->nullable()->after('city_id');
                $table->string('longitude')->nullable()->after('latitude');
                $table->boolean('converted_to_suffragan')->default(false)->after('longitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropColumn([
                'document_number',
                'phone',
                'email',
                'address',
                'city_id',
                'latitude',
                'longitude',
                'converted_to_suffragan',
            ]);
        });
    }
};
