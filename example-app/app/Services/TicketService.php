<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TicketService
{
    /**
     * Obtiene los tickets filtrados según la búsqueda y el tipo de usuario.
     */
    public function getTicketsForBoard(?string $search = null): Collection
    {
        $user = auth()->user();
        if (! $user) {
            return collect();
        }

        $query = Ticket::with(['creator', 'assignee']);

        // Si es cliente, solo ve sus propios tickets
        if ($user->type === 'client') {
            $query->where('created_by', $user->id);
        }

        // Búsqueda por título o número de ticket
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Obtiene la estructura de columnas dinámicas según el modo de vista.
     */
    public function getBoardColumns(string $viewMode): array
    {
        if ($viewMode === 'by_assignee') {
            $employees = User::whereIn('type', ['employee', 'admin'])
                ->get()
                ->map(fn ($user) => [
                    'id' => (string) $user->id,
                    'title' => $user->name,
                    'avatar' => strtoupper(substr($user->name, 0, 2)),
                ])
                ->toArray();

            // Columna por defecto para tickets no asignados
            array_unshift($employees, [
                'id' => 'unassigned',
                'title' => 'Sin Asignar',
                'avatar' => '?',
            ]);

            return $employees;
        }

        // Vista por Estado
        return [
            ['id' => 'open', 'title' => '🔴 Por Hacer (Open)'],
            ['id' => 'in_progress', 'title' => '🟡 En Progreso'],
            ['id' => 'resolved', 'title' => '🟢 Resueltos'],
            ['id' => 'closed', 'title' => '⚪ Cerrados'],
        ];
    }

    /**
     * Crear un nuevo ticket.
     */
    public function createTicket(array $data): Ticket
    {
        return Ticket::create([
            'title' => $data['title'],
            'priority' => $data['priority'] ?? 'medium',
            'description' => $data['description'],
            'assigned_to' => $data['assigned_to'] ?? null,
            'status' => $data['status'] ?? 'open',
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Actualizar los datos de un ticket existente.
     */
    public function updateTicket(Ticket $ticket, array $data): bool
    {
        return $ticket->update([
            'title' => $data['title'] ?? $ticket->title,
            'priority' => $data['priority'] ?? $ticket->priority,
            'status' => $data['status'] ?? $ticket->status,
            'assigned_to' => $data['assigned_to'] ?? $ticket->assigned_to,
            'description' => $data['description'] ?? $ticket->description,
        ]);
    }

    /**
     * Lógica de Drop / Arrastrar:
     * Actualiza el atributo según la columna donde fue soltado el ticket.
     */
    public function moveTicket(int|string $ticketId, string $targetColumnId, string $viewMode): bool
    {
        $ticket = Ticket::find($ticketId);

        if (! $ticket) {
            return false;
        }

        if ($viewMode === 'by_assignee') {
            // Si la columna es "unassigned", el valor pasa a null
            $assigneeId = $targetColumnId === 'unassigned' ? null : $targetColumnId;
            return $ticket->update(['assigned_to' => $assigneeId]);
        }

        // Modo 'by_status'
        return $ticket->update(['status' => $targetColumnId]);
    }

    /**
     * Eliminar un ticket por ID o Modelo.
     */
    public function deleteTicket(int|string|Ticket $ticket): ?bool
    {
        if (! ($ticket instanceof Ticket)) {
            $ticket = Ticket::find($ticket);
        }

        return $ticket ? $ticket->delete() : false;
    }
}
