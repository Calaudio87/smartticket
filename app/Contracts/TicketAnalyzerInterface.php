<?php


namespace App\Contracts;

interface TicketAnalyzerInterface
{
    /**
     * Analiza el ticket y retorna un arreglo con: category, priority y summary.
     */
    public function analyze(string $subject, string $description): array;
}
