<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Suffragan;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class AttendanceController extends Controller
{
    public function storeAttendance(Request $request, Event $event)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:suffragans,phone',
            'documentationtype' => 'required|string|max:50',
            'documentationnumber' => 'required|string|max:50|unique:suffragans,documentationnumber',
        ]);

        // Buscar o crear al sufragante
        $suffragan = Suffragan::firstOrCreate(
            [
                'phone' => $validated['phone'],
                'documentationnumber' => $validated['documentationnumber'],
            ],
            $validated
        );

        // Capturar datos del dispositivo
        $agent = new Agent();
        $suffragan->update([
            'user_agent' => $request->header('User-Agent'),
            'platform' => $agent->platform(),
            'language' => $request->server('HTTP_ACCEPT_LANGUAGE'),
            'timezone' => $request->header('Time-Zone'),
        ]);

        // Registrar asistencia en la tabla pivote
        $event->suffragans()->syncWithoutDetaching([
            $suffragan->id => ['attended_at' => now()],
        ]);

        return response()->json([
            'message' => 'Asistencia registrada correctamente.',
            'suffragan' => $suffragan,
            'action' => [
                'label' => 'Volver', // Texto del botón
                'url' => url()->previous() // URL a la que debe llevar el botón (-1 o página anterior)
            ]
        ]);
    }

    public function showForm(Event $event)
    {
        return view('attendance.form', ['event' => $event]);
    }
}
