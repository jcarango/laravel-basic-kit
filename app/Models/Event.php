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
    ];

    protected static function booted()
    {
        // Usa el evento "created" en lugar de "creating"
        static::created(function ($event) {
            $event->generateQrCode();
        });
    }

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

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
