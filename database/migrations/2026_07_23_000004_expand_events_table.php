<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // General
            if (!Schema::hasColumn('events', 'event_date')) {
                $table->date('event_date')->nullable();
            }
            if (!Schema::hasColumn('events', 'responsible_name')) {
                $table->string('responsible_name')->nullable();
            }
            if (!Schema::hasColumn('events', 'city_id')) {
                $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            }
            if (!Schema::hasColumn('events', 'barrio')) {
                $table->string('barrio')->nullable();
            }
            if (!Schema::hasColumn('events', 'latitude')) {
                $table->string('latitude')->nullable();
            }
            if (!Schema::hasColumn('events', 'longitude')) {
                $table->string('longitude')->nullable();
            }

            // Planeación
            if (!Schema::hasColumn('events', 'objectives')) {
                $table->text('objectives')->nullable();
            }
            if (!Schema::hasColumn('events', 'budget')) {
                $table->decimal('budget', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('events', 'resources_needed')) {
                $table->text('resources_needed')->nullable();
            }
            if (!Schema::hasColumn('events', 'staff_needed')) {
                $table->text('staff_needed')->nullable();
            }
            if (!Schema::hasColumn('events', 'transport_details')) {
                $table->text('transport_details')->nullable();
            }
            if (!Schema::hasColumn('events', 'catering_details')) {
                $table->text('catering_details')->nullable();
            }
            if (!Schema::hasColumn('events', 'logistics_notes')) {
                $table->text('logistics_notes')->nullable();
            }

            // Avanzada
            if (!Schema::hasColumn('events', 'pre_visits_notes')) {
                $table->text('pre_visits_notes')->nullable();
            }
            if (!Schema::hasColumn('events', 'pre_meetings_notes')) {
                $table->text('pre_meetings_notes')->nullable();
            }
            if (!Schema::hasColumn('events', 'permits_status')) {
                $table->string('permits_status')->nullable();
            }
            if (!Schema::hasColumn('events', 'publicity_notes')) {
                $table->text('publicity_notes')->nullable();
            }
            if (!Schema::hasColumn('events', 'sound_system_notes')) {
                $table->text('sound_system_notes')->nullable();
            }
            if (!Schema::hasColumn('events', 'stage_notes')) {
                $table->text('stage_notes')->nullable();
            }
            if (!Schema::hasColumn('events', 'security_notes')) {
                $table->text('security_notes')->nullable();
            }
            if (!Schema::hasColumn('events', 'guests_list')) {
                $table->text('guests_list')->nullable();
            }

            // Durante el evento
            if (!Schema::hasColumn('events', 'expected_attendance')) {
                $table->integer('expected_attendance')->nullable();
            }
            if (!Schema::hasColumn('events', 'real_attendance')) {
                $table->integer('real_attendance')->nullable();
            }
            if (!Schema::hasColumn('events', 'photos')) {
                $table->json('photos')->nullable();
            }
            if (!Schema::hasColumn('events', 'videos')) {
                $table->json('videos')->nullable();
            }
            if (!Schema::hasColumn('events', 'during_notes')) {
                $table->text('during_notes')->nullable();
            }

            // Después del evento
            if (!Schema::hasColumn('events', 'result_summary')) {
                $table->text('result_summary')->nullable();
            }
            if (!Schema::hasColumn('events', 'political_impact')) {
                $table->text('political_impact')->nullable();
            }
            if (!Schema::hasColumn('events', 'commitments_acquired')) {
                $table->text('commitments_acquired')->nullable();
            }
            if (!Schema::hasColumn('events', 'followup_notes')) {
                $table->text('followup_notes')->nullable();
            }
            if (!Schema::hasColumn('events', 'evidences')) {
                $table->json('evidences')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'event_date', 'responsible_name', 'city_id', 'barrio', 'latitude', 'longitude',
                'objectives', 'budget', 'resources_needed', 'staff_needed', 'transport_details', 'catering_details', 'logistics_notes',
                'pre_visits_notes', 'pre_meetings_notes', 'permits_status', 'publicity_notes', 'sound_system_notes', 'stage_notes', 'security_notes', 'guests_list',
                'expected_attendance', 'real_attendance', 'photos', 'videos', 'during_notes',
                'result_summary', 'political_impact', 'commitments_acquired', 'followup_notes', 'evidences'
            ]);
        });
    }
};
