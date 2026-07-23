<?php

namespace App\Livewire;

use Livewire\Component;

use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Suffragan;
use App\Models\Resume;

class HojaDeVidaForm extends Component
{
    use WithFileUploads;

    public $tipo_ingreso = 'archivo'; // 'archivo' o 'formulario'
    public $identificacion;
    public $perfil_profesional;
    public $archivo_pdf;
    public $experiencia;
    public $educacion;
    public $referencias;
    public $habeas_data_accepted = false;
    
    public $successMessage = '';
    public $errorMessage = '';

    public function submit()
    {
        $this->errorMessage = '';
        $this->successMessage = '';

        $rules = [
            'identificacion' => 'required|string',
            'perfil_profesional' => 'required|string|max:2000',
            'habeas_data_accepted' => 'accepted'
        ];

        if ($this->tipo_ingreso === 'archivo') {
            $rules['archivo_pdf'] = 'required|file|mimes:pdf|max:20480'; // 20MB
        } else {
            $rules['experiencia'] = 'required|string|max:2000';
            $rules['educacion'] = 'required|string|max:2000';
            $rules['referencias'] = 'required|string|max:2000';
        }

        $this->validate($rules, [
            'identificacion.required' => 'El campo identificación es obligatorio.',
            'perfil_profesional.required' => 'El perfil profesional es obligatorio.',
            'perfil_profesional.max' => 'El perfil no puede superar los 2000 caracteres.',
            'archivo_pdf.required' => 'Debes adjuntar tu hoja de vida en PDF.',
            'archivo_pdf.mimes' => 'El archivo debe ser un PDF.',
            'archivo_pdf.max' => 'El tamaño del PDF no debe superar los 20MB.',
            'experiencia.required' => 'La experiencia es obligatoria.',
            'educacion.required' => 'La educación es obligatoria.',
            'referencias.required' => 'Las referencias son obligatorias.',
            'habeas_data_accepted.accepted' => 'Debes aceptar la política de Habeas Data para continuar.'
        ]);

        // Search for user or suffragan
        $user = User::where('email', $this->identificacion)->first();
        $suffragan = Suffragan::where('documentationnumber', $this->identificacion)
                              ->orWhere('email', $this->identificacion)
                              ->first();

        if (!$user && !$suffragan) {
            $this->errorMessage = 'No encontramos ningún registro con esta identificación (Cédula) o correo electrónico en el sistema. Verifica que estés registrado.';
            return;
        }

        $path = null;
        if ($this->tipo_ingreso === 'archivo' && $this->archivo_pdf) {
            $path = $this->archivo_pdf->store('resumes', 'public');
        }

        // Check if Resume already exists
        $resumeData = [
            'identificacion' => $this->identificacion,
            'perfil_profesional' => $this->perfil_profesional,
            'archivo_pdf' => $path,
            'experiencia' => $this->tipo_ingreso === 'formulario' ? $this->experiencia : null,
            'educacion' => $this->tipo_ingreso === 'formulario' ? $this->educacion : null,
            'referencias' => $this->tipo_ingreso === 'formulario' ? $this->referencias : null,
        ];

        if ($user) {
            $resumeData['user_id'] = $user->id;
            $user->update(['habeas_data_accepted' => true]);
        }
        
        if ($suffragan) {
            $resumeData['suffragan_id'] = $suffragan->id;
            $suffragan->update(['habeas_data_accepted' => true]);
        }

        Resume::create($resumeData);

        $this->successMessage = '¡Tu Hoja de Vida ha sido enviada correctamente y hemos registrado tu aceptación al Tratamiento de Datos (Habeas Data)!';
        
        // Reset form
        $this->reset(['identificacion', 'perfil_profesional', 'archivo_pdf', 'experiencia', 'educacion', 'referencias', 'habeas_data_accepted']);
    }

    public function render()
    {
        return view('livewire.hoja-de-vida-form');
    }
}
