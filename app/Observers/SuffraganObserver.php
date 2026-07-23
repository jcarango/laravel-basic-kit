<?php

namespace App\Observers;

use App\Models\Suffragan;

class SuffraganObserver
{
    /**
     * Handle the Suffragan "saving" event.
     */
    public function saving(Suffragan $suffragan): void
    {
        // El cálculo del perfil ha sido trasladado a la capa del Modelo y ejecutado por el Resource
        // luego de asegurar que las relaciones con Pivot se han procesado correctamente.
    }

    /**
     * Handle the Suffragan "created" event.
     */
    public function created(Suffragan $suffragan): void
    {
        // Auto-crear perfil (resume) en blanco para todos los nuevos sufragantes si no lo tiene.
        if (! $suffragan->resume) {
            \App\Models\Resume::withoutEvents(function () use ($suffragan) {
                \App\Models\Resume::create([
                    'suffragan_id' => $suffragan->id,
                    'profile_score' => 0,
                    'is_available_for_committees' => false,
                ]);
            });
        }
    }

    /**
     * Handle the Suffragan "updated" event.
     */
    public function updated(Suffragan $suffragan): void
    {
        //
    }

    /**
     * Handle the Suffragan "deleted" event.
     */
    public function deleted(Suffragan $suffragan): void
    {
        if ($suffragan->resume) {
            $suffragan->resume->delete();
        }
    }
}
