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
        Schema::create('suffragans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('lastname');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('documentationtype');
            $table->string('documentationnumber');
            $table->string('latitude_address')->nullable();
            $table->string('longitude_address')->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('state_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('address')->nullable();
            $table->string('profession')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('photo')->nullable();
            $table->foreignId('categories_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('mesa')->nullable();
            $table->foreignId('divipol_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('suffragan_id')->nullable()->constrained('suffragans')->onUpdate('cascade')->onDelete('cascade');
            $table->string('user_agent')->nullable();
            $table->string('platform')->nullable();
            $table->string('language')->nullable();
            $table->string('screen_resolution')->nullable();
            $table->string('timezone')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suffragans');
    }
};
