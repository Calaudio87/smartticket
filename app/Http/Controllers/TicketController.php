<?php

// app/Http/Controllers/TicketController.php
namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Http\Requests\StoreTicketRequest;
use App\Services\Contracts\TicketAnalyzerInterface;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function store(StoreTicketRequest $request, TicketAnalyzerInterface $analyzer)
    {
        $validated = $request->validated();

        try {
            $analysis = $analyzer->analyze($validated['subject'], $validated['description']);
        } catch (\Exception $e) {
            Log::error('Fallo en el servicio de triaje (TicketAnalyzer): ' . $e->getMessage());

            // Fallback seguro si la IA (o el Mock) se cae
            $analysis = [
                'category' => 'Otros',
                'priority' => 'Media',
                'summary' => 'Pendiente de triaje manual debido a un error del sistema.',
            ];
        }

        Ticket::create(array_merge($validated, [
            'category' => $analysis['category'],
            'priority' => $analysis['priority'],
            'summary' => $analysis['summary'],
            'status' => 'open',
        ]));

        return back()->with('success', 'Ticket enviado exitosamente.');
    }
}
