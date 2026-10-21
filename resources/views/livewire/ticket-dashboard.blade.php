<div>
    <!-- Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border border-gray-100 flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500 font-medium">Tickets Abiertos</p>
                <p class="text-2xl font-bold text-gray-800">{{ $openTickets }}</p>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-100 flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500 font-medium">Prioridad Crítica</p>
                <p class="text-2xl font-bold text-red-600">{{ $criticalPercent }}%</p>
            </div>
            <div class="p-3 bg-red-50 text-red-600 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-4 rounded-t-lg shadow border-b border-gray-200 flex flex-col md:flex-row gap-4 justify-between items-center">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por asunto o cliente..." class="w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">

        <div class="flex gap-4 w-full md:w-auto">
            <select wire:model.live="status" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Todos los Estados</option>
                <option value="open">Abierto</option>
                <option value="in_progress">En Progreso</option>
                <option value="resolved">Resuelto</option>
            </select>
            <select wire:model.live="priority" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Todas las Prioridades</option>
                <option value="Baja">Baja</option>
                <option value="Media">Media</option>
                <option value="Alta">Alta</option>
                <option value="Crítica">Crítica</option>
            </select>
        </div>
    </div>

    <!-- Listado de Tickets -->
    <div class="bg-white shadow rounded-b-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clasificación</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notas</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            @forelse($tickets as $ticket)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $ticket->subject }}</div>
                        <div class="text-sm text-gray-500">{{ $ticket->name }}</div>
                        <div class="text-xs text-indigo-600 mt-1 italic">{{ $ticket->summary }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $ticket->category }}</span>
                        <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $ticket->priority === 'Crítica' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $ticket->priority }}
                            </span>
                    </td>
                    <td class="px-6 py-4">
                        <select wire:change="changeStatus({{ $ticket->id }}, $event.target.value)" class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Abierto</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resuelto</option>
                        </select>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $ticket->notes_count }} notas
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">No se encontraron tickets.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-200">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
