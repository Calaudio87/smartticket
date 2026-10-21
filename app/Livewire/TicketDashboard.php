<?php

// app/Livewire/TicketDashboard.php
namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;

class TicketDashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $priority = ''; // Agregado para métricas/filtros extra

    // Resetear paginación al usar filtros (Solución a Bug de UX)
    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatus() { $this->resetPage(); }
    public function updatedPriority() { $this->resetPage(); }

    public function changeStatus($ticketId, $newStatus)
    {
        // Solución a la vulnerabilidad de inyección: Validar contra estados permitidos
        $allowedStatuses = ['open', 'in_progress', 'resolved'];

        if (!in_array($newStatus, $allowedStatuses)) {
            abort(403, 'Estado de ticket no válido.');
        }

        Ticket::where('id', $ticketId)->update(['status' => $newStatus]);
    }

    public function render()
    {
        $tickets = Ticket::query()
            // Solución a bug silencioso de PostgreSQL: Uso de ilike para case-insensitive
            ->when($this->search, fn($q) => $q->where('subject', 'ilike', "%{$this->search}%"))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->priority, fn($q) => $q->where('priority', $this->priority))
            ->withCount('notes')
            ->latest()
            ->paginate(10);

        return view('livewire.ticket-dashboard', compact('tickets'));
    }
}
