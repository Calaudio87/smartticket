<?php

namespace App\Actions;

use App\Contracts\TicketAnalyzerInterface;
use App\Models\Ticket;

class CreateTicketAction
{
    public function __construct(protected TicketAnalyzerInterface $analyzer)
    {
    }

    public function execute(array $data): Ticket
    {
        // Limpiamos la entrada para evitar XSS básico
        $data['description'] = strip_tags($data['description']);

        // 1. Analizamos el ticket con el motor (Mock o IA)
        $analysis = $this->analyzer->analyze($data['subject'], $data['description']);

        // 2. Combinamos los datos del usuario con el resultado del triaje
        $ticketData = array_merge($data, [
            'category' => $analysis['category'],
            'priority' => $analysis['priority'],
            'summary'  => $analysis['summary'],
            'status'   => 'open',
        ]);

        // 3. Guardamos en la base de datos
        return Ticket::create($ticketData);
    }
}
