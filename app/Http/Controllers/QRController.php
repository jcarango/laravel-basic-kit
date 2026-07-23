<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRController extends Controller
{
    public function generateSuffraganQR()
    {
        // URL para la creación de Suffragan
        $url = route('filament.resources.suffragans.create');

        // Generar código QR
        $qrCode = QrCode::size(300)->generate($url);

        // Retornar una vista que muestre el QR
        return view('qr.suffragan', compact('qrCode'));
    }
}