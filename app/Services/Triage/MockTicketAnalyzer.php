<?php


namespace App\Services\Triage;

use App\Contracts\TicketAnalyzerInterface;
use Illuminate\Support\Str;

class MockTicketAnalyzer implements TicketAnalyzerInterface
{
    public function analyze(string $subject, string $description): array
    {
        $text = strtolower($subject . ' ' . $description);

        $category = 'Otros';
        if (Str::contains($text, ['factura', 'pago', 'cobro', 'tarjeta'])) {
            $category = 'Facturación';
        } elseif (Str::contains($text, ['error', 'bug', 'falla', 'no funciona'])) {
            $category = 'Soporte Técnico';
        } elseif (Str::contains($text, ['hack', 'spam', 'contraseña', 'acceso'])) {
            $category = 'Seguridad / Abuso';
        }

        $priority = 'Baja';
        if (Str::contains($text, ['urgente', 'crítico', 'caído', 'producción'])) {
            $priority = 'Crítica';
        } elseif (Str::contains($text, ['bloqueado', 'pagos'])) {
            $priority = 'Alta';
        } elseif (Str::contains($text, ['error', 'bug'])) {
            $priority = 'Media';
        }

        return [
            'category' => $category,
            'priority' => $priority,
            'summary' => Str::limit($description, 60),
        ];
    }
}
