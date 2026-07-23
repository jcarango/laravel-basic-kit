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
        Schema::dropIfExists('committee_user');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['candidate_id']);
            $table->dropColumn(['candidate_id', 'latitude', 'longitude']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->foreignId('suffragan_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('suffragans', function (Blueprint $table) {
            $table->foreignId('candidate_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('suffragans', function (Blueprint $table) {
            $table->dropForeign(['candidate_id']);
            $table->dropColumn('candidate_id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['suffragan_id']);
            $table->dropColumn('suffragan_id');
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('candidate_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
        });

        Schema::create('committee_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('committee_id')->constrained('political_committees')->onUpdate('cascade')->onDelete('cascade');
            $table->string('role')->nullable();
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
        });
    }
};
