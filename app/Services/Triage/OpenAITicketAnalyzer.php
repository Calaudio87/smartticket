<?php

namespace App\Services\Triage;

use App\Contracts\TicketAnalyzerInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OpenAITicketAnalyzer implements TicketAnalyzerInterface
{
    public function analyze(string $subject, string $description): array
    {
        $defaultFallback = [
            'category' => 'Otros',
            'priority' => 'Media',
            'summary'  => Str::limit($description, 60)
        ];

        try {
            $response = Http::withToken(config('services.openai.key'))
                ->timeout(10)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Eres un experto en soporte IT. Clasifica el ticket devolviendo un JSON estricto con las claves: "category" (Facturación, Soporte Técnico, Seguridad / Abuso, Otros), "priority" (Baja, Media, Alta, Crítica) y "summary" (resumen de máximo 10 palabras).'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Asunto: $subject\nDescripción: $description"
                        ]
                    ],
                    'response_format' => ['type' => 'json_object']
                ]);

            if ($response->successful()) {
                return json_decode($response->json('choices.0.message.content'), true);
            }

            Log::error('Fallo en la API de OpenAI', ['response' => $response->body()]);
            return $defaultFallback;

        } catch (\Exception $e) {
            Log::error('Excepción al conectar con OpenAI: ' . $e->getMessage());
            return $defaultFallback;
        }
    }
}
