<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'color', 'starts_at', 'ends_at', 'qr_code_path',
        'suffragan_id', 'candidate_id',
        'event_date', 'responsible_name', 'city_id', 'barrio', 'latitude', 'longitude',
        'objectives', 'budget', 'resources_needed', 'staff_needed', 'transport_details', 'catering_details', 'logistics_notes',
        'pre_visits_notes', 'pre_meetings_notes', 'permits_status', 'publicity_notes', 'sound_system_notes', 'stage_notes', 'security_notes', 'guests_list',
        'expected_attendance', 'real_attendance', 'photos', 'videos', 'during_notes',
        'result_summary', 'political_impact', 'commitments_acquired', 'followup_notes', 'evidences',
    ];

    protected static function booted()
    {
        static::created(function ($event) {
            $event->generateQrCode();
        });
    }

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'event_date' => 'date',
        'budget' => 'decimal:2',
        'expected_attendance' => 'integer',
        'real_attendance' => 'integer',
        'photos' => 'array',
        'videos' => 'array',
        'evidences' => 'array',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }


    public function generateQrCode()
    {
        // Generar la URL con el ID del evento
        $publicUrl = route('attendance.form', ['event' => $this->id]);

        $uniqueName = 'event_' . uniqid() . '.svg';
        $filePath = 'qrcodes/' . $uniqueName;

        // Generar el código QR
        $qrCode = QrCode::format('svg')
            ->size(600)
            ->generate($publicUrl);

        // Guardar el archivo en el disco público
        Storage::disk('public')->put($filePath, $qrCode);

        // Actualizar el campo en la base de datos
        $this->qr_code_path = $filePath;
        $this->save();
    }

    public function suffragans()
    {
        return $this->belongsToMany(Suffragan::class, 'event_suffragan')
            ->withPivot('attended_at')
            ->withTimestamps();
    }

    public function leader()
    {
        return $this->belongsTo(Suffragan::class, 'suffragan_id');
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
