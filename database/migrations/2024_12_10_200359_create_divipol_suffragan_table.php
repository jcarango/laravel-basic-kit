<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDivipolSuffraganTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suffragan_divipols', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suffragan_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('divipol_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('valor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suffragan_divipols');
    }
}
