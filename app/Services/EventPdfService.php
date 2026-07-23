<?php

namespace App\Services;

use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EventPdfService
{
    public static function generatePdf(Event $event)
    {
        $attendanceUrl = route('attendance.form', ['event' => $event->id]);

        $qrCodePng = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->generate($attendanceUrl);

        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodePng);

        $pdf = Pdf::loadView('events.pdf', [
            'event' => $event,
            'qrCode' => $qrCodeBase64,
            'attendanceUrl' => $attendanceUrl,
        ]);

        return $pdf;
    }
}
