<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;

class TicketDashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $priority = '';

    // Reiniciar paginación al filtrar
    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }
    public function updatingPriority() { $this->resetPage(); }

    public function changeStatus($ticketId, $newStatus)
    {
        Ticket::where('id', $ticketId)->update(['status' => $newStatus]);
    }

    public function render()
    {
        // Métricas rápidas
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', 'open')->count();
        $criticalCount = Ticket::where('priority', 'Crítica')->count();
        $criticalPercent = $totalTickets > 0 ? round(($criticalCount / $totalTickets) * 100) : 0;

        // Consulta eficiente de tickets
        $tickets = Ticket::query()
            ->when($this->search, function ($query) {
                $query->where('subject', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%");
            })
            ->when($this->status, fn($query) => $query->where('status', $this->status))
            ->when($this->priority, fn($query) => $query->where('priority', $this->priority))
            ->withCount('notes') // Evita la trampa del N+1 al contar notas
            ->latest()
            ->paginate(10);

        return view('livewire.ticket-dashboard', [
            'tickets' => $tickets,
            'openTickets' => $openTickets,
            'criticalPercent' => $criticalPercent,
        ]);
    }
}
