<?php

namespace App\Jobs;

use App\Models\Estrategia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessEstrategiaAnalysis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $estrategia;

    public function __construct(Estrategia $estrategia)
    {
        $this->estrategia = $estrategia;
    }

    public function handle()
    {
        try {
            // Actualizar estado a procesando
            $this->estrategia->update(['analisis_status' => 'procesando']);
            
            // Construir el prompt con todos los campos relevantes
            $prompt = "Eres un consultor estratégico político. Analiza la siguiente información de un candidato:\n\n";
            
            $campos = [
                'Por qué quiere ser' => $this->estrategia->quiereser,
                'Determinó su propia imagen' => $this->estrategia->determinoimagen,
                'Identificó problemas claves' => $this->estrategia->identificoproblemas,
                'Identificó seguidores' => $this->estrategia->identificoseguidores,
                'Identificó capacidad de recursos' => $this->estrategia->identificocapacidad,
                'Qué le interesa que sepa la gente' => $this->estrategia->iteresproyecto,
                'Qué lo hace mejor' => $this->estrategia->mejorqueotros,
                'Propuesta' => $this->estrategia->Propuesta,
                'Sector priorizado' => $this->estrategia->sectorpriorizado,
                'Problemática determinada' => $this->estrategia->problematicadeterminada,
                'Objetivo general' => $this->estrategia->objetivogeneral,
                'Objetivos estratégicos' => $this->estrategia->objetivosestrategicos,
                'Planeación estratégica' => $this->estrategia->planeacionestrategia,
                'Plan de desarrollo' => $this->estrategia->plandesarrollo,
                'Plan de acción' => $this->estrategia->planproceso,
                'Plan de mejoramiento' => $this->estrategia->planmejoramiento,
                'Situación real' => $this->estrategia->situacionreal,
                'Insumos' => $this->estrategia->insumos,
                'Procesos' => $this->estrategia->procesos,
                'Productos' => $this->estrategia->productos,
                'Resultados' => $this->estrategia->resultados,
                'Impactos' => $this->estrategia->impactos,
                'Situación lograble' => $this->estrategia->situacionlograble,
            ];

            foreach ($campos as $titulo => $valor) {
                if (!empty($valor)) {
                    $prompt .= "**{$titulo}**:\n{$valor}\n\n";
                }
            }

            $prompt .= "\nRealiza un análisis estratégico completo. Incluye:\n";
            $prompt .= "- Resumen ejecutivo\n";
            $prompt .= "- Fortalezas y debilidades del candidato\n";
            $prompt .= "- Oportunidades y amenazas\n";
            $prompt .= "- Recomendaciones clave para la campaña\n";
            $prompt .= "- Sugerencias de comunicación\n";
            $prompt .= "Responde en formato JSON con las siguientes claves: resumen, fortalezas, debilidades, oportunidades, amenazas, recomendaciones, comunicacion.";

            // Llamar a la API de DeepSeek
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.deepseek.api_key'),
                'Content-Type' => 'application/json',
            ])->post(config('services.deepseek.api_url'), [
                'model' => 'deepseek-chat',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 4096,
                'response_format' => ['type' => 'json_object'] // Forzar respuesta JSON
            ]);

            if ($response->successful()) {
                $analisis = json_decode($response->json('choices.0.message.content'), true);
                
                $this->estrategia->update([
                    'analisis' => $analisis['resumen'] ?? 'Análisis no disponible',
                    'analisis_detallado' => $analisis,
                    'analisis_status' => 'completado'
                ]);
                
            } else {
                Log::error('Error en API DeepSeek', ['response' => $response->body()]);
                $this->estrategia->update(['analisis_status' => 'error']);
            }
            
        } catch (\Exception $e) {
            Log::error('Error procesando análisis', ['error' => $e->getMessage()]);
            $this->estrategia->update(['analisis_status' => 'error']);
        }
    }
}