<?php

// app/Http/Controllers/EstrategiaController.php

namespace App\Http\Controllers;

use App\Models\Estrategia;
use Illuminate\Http\Request;

class EstrategiaController extends Controller
{
    public function index()
    {
        $estrategias = Estrategia::all();
        return view('estrategias.index', compact('estrategias'));
    }

    public function dofa(Estrategia $estrategia)
    {
        // Lógica para generar DOFA según los campos de la estrategia
        // Podrías procesar los datos y pasarlos a una vista para visualizarlos o crear un archivo PDF/Word
        return view('estrategias.dofa', compact('estrategia'));
    }
}
