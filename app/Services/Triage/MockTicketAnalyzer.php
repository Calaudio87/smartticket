<?php

// app/Services/Analyzers/MockTicketAnalyzer.php
namespace App\Services\Analyzers;

use App\Services\Contracts\TicketAnalyzerInterface;

class MockTicketAnalyzer implements TicketAnalyzerInterface
{
    public function analyze(string $subject, string $description): array
    {
        // Uso de mb_strtolower para manejar tildes y caracteres especiales en español
        $text = mb_strtolower($subject . ' ' . $description, 'UTF-8');

        $priority = str_contains($text, 'urgente') || str_contains($text, 'crítico') ? 'Crítica' : 'Media';

        if (str_contains($text, 'factura') || str_contains($text, 'pago')) {
            $category = 'Facturación';
        } elseif (str_contains($text, 'seguridad') || str_contains($text, 'abuso') || str_contains($text, 'hackeo')) {
            $category = 'Seguridad / Abuso';
        } elseif (str_contains($text, 'error') || str_contains($text, 'falla')) {
            $category = 'Soporte Técnico';
        } else {
            $category = 'Otros';
        }

        return [
            'category' => $category,
            'priority' => $priority,
            'summary' => 'Clasificación generada por Mock Analyzer.',
        ];
    }
}
