<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Spatie\ModelStates\Exceptions\TransitionNotFound;
use App\Models\Ticket;
use App\Models\User;
use App\States\Open;
use App\States\InProgress;
use App\States\Closed;

class TicketBoard extends Page
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-view-columns';
    protected static string|null|\UnitEnum $navigationGroup = 'Soporte';

    protected static ?string $navigationLabel = 'Tablero';
    protected static ?string $title = 'Tickets';
    protected string $view = 'filament.pages.ticket-board';

    public string $viewMode = 'status';
    public string $searchQuery = '';

    /**
     * Acción nativa de Filament para Crear un Nuevo Ticket
     */
    public function createTicketAction(): Action
    {
        return Action::make('createTicket')
            ->label('Nuevo Ticket')
            ->modalHeading('Crear Nuevo Ticket')
            ->modalWidth('lg')
            ->form([
                TextInput::make('ticket_number')
                    ->label('Folio')
                    ->default(fn () => 'TK-' . strtoupper(uniqid()))
                    ->required()
                    ->unique(Ticket::class, 'ticket_number'),

                TextInput::make('title')
                    ->label('Título del ticket')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3),

                Select::make('assigned_to')
                    ->label('Asignar a empleado')
                    ->options(fn () => User::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
            ])
            ->action(function (array $data): void {
                Ticket::create($data);

                Notification::make()
                    ->title('Ticket creado con éxito')
                    ->success()
                    ->send();
            });
    }
    public function editTicketAction(): Action
    {
        return Action::make('editTicket')
            ->label('Detalles del Ticket')
            ->modalHeading(fn (array $arguments) => 'Editar Ticket #' . (Ticket::find($arguments['ticketId'] ?? null)?->ticket_number ?? ''))
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Guardar Cambios')
            ->fillForm(function (array $arguments): array {
                $ticket = Ticket::find($arguments['ticketId'] ?? null);

                if (! $ticket) {
                    return [];
                }

                return [
                    'ticket_number' => $ticket->ticket_number,
                    'title'         => $ticket->title,
                    'description'   => $ticket->description,
                    'assigned_to'   => $ticket->assigned_to,
                    'status'        => $ticket->status::$name ?? Open::$name,
                ];
            })
            ->form([
                TextInput::make('ticket_number')
                    ->label('Folio')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('title')
                    ->label('Título del ticket')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(4),

                Select::make('status')
                    ->label('Estatus del Ticket')
                    ->options([
                        Open::$name       => 'Abierto',
                        InProgress::$name => 'En Progreso',
                        Closed::$name     => 'Cerrado',
                    ])
                    ->required(),

                Select::make('assigned_to')
                    ->label('Empleado Asignado')
                    ->options(fn () => User::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
            ])
            ->action(function (array $data, array $arguments): void {
                $ticket = Ticket::find($arguments['ticketId'] ?? null);

                if (! $ticket) {
                    return;
                }

                // 1. Actualizar campos de texto y asignación
                $ticket->title = $data['title'];
                $ticket->description = $data['description'];
                $ticket->assigned_to = $data['assigned_to'] ?: null;
                $ticket->save();

                // 2. Transicionar el estado si cambió
                $newStatus = $data['status'];
                if ($ticket->status::$name !== $newStatus) {
                    try {
                        $ticket->status->transitionTo($newStatus);
                    } catch (TransitionNotFound $e) {
                        Notification::make()
                            ->title('Estado no actualizado')
                            ->body('La transición de estado no está permitida por las reglas del flujo.')
                            ->warning()
                            ->send();
                    }
                }

                Notification::make()
                    ->title('Ticket actualizado')
                    ->body("El ticket #{$ticket->ticket_number} fue modificado correctamente.")
                    ->success()
                    ->send();
            });
    }

    public function getColumnsProperty(): array
    {
        if ($this->viewMode === 'assignee') {
            $columns = [
                [
                    'id' => 'unassigned',
                    'title' => 'Sin Asignar',
                    'badge_color' => 'bg-gray-100 text-gray-800 border-gray-300 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/30',
                    'card_border' => 'border-l-gray-400 dark:border-l-gray-500',
                    'match' => fn ($ticket) => is_null($ticket->assigned_to),
                ],
            ];

            foreach ($this->users as $user) {
                $columns[] = [
                    'id' => (string) $user->id,
                    'title' => $user->name,
                    'badge_color' => 'bg-indigo-100 text-indigo-800 border-indigo-300 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/30',
                    'card_border' => 'border-l-indigo-500 dark:border-l-indigo-400',
                    'match' => fn ($ticket) => (string) $ticket->assigned_to === (string) $user->id,
                ];
            }

            return $columns;
        }

        return [
            [
                'id' => Open::$name,
                'title' => 'Abierto',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/30',
                'card_border' => 'border-l-amber-500 dark:border-l-amber-400',
                'match' => fn ($ticket) => $ticket->status::$name === Open::$name,
            ],
            [
                'id' => InProgress::$name,
                'title' => 'En Progreso',
                'badge_color' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30',
                'card_border' => 'border-l-blue-500 dark:border-l-blue-400',
                'match' => fn ($ticket) => $ticket->status::$name === InProgress::$name,
            ],
            [
                'id' => Closed::$name,
                'title' => 'Cerrado',
                'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/30',
                'card_border' => 'border-l-emerald-500 dark:border-l-emerald-400',
                'match' => fn ($ticket) => $ticket->status::$name === Closed::$name,
            ],
        ];
    }

    public function getTicketsProperty()
    {
        return Ticket::with(['assignee'])->get();
    }

    public function getFilteredTicketsProperty()
    {
        return $this->tickets->filter(function ($ticket) {
            if (empty($this->searchQuery)) {
                return true;
            }

            $search = strtolower($this->searchQuery);

            return str_contains(strtolower($ticket->title), $search)
                || str_contains(strtolower($ticket->ticket_number), $search);
        });
    }

    public function getUsersProperty()
    {
        return User::orderBy('name')->get();
    }

    public function handleDrop($ticketId, $targetId): void
    {
        if ($this->viewMode === 'assignee') {
            $userId = $targetId === 'unassigned' ? null : $targetId;
            $this->updateTicketAssignee($ticketId, $userId);
        } else {
            $this->updateTicketColumn($ticketId, $targetId);
        }
    }

    public function updateTicketColumn($ticketId, $newStatusName): void
    {
        $ticket = Ticket::find($ticketId);

        if (! $ticket || ! $ticket->status || $ticket->status::$name === $newStatusName) {
            return;
        }

        try {
            $ticket->status->transitionTo($newStatusName);

            Notification::make()
                ->title('Estado actualizado')
                ->body("El ticket #{$ticket->ticket_number} se movió correctamente.")
                ->success()
                ->send();

        } catch (TransitionNotFound $e) {
            Notification::make()
                ->title('Movimiento no permitido')
                ->body('La transición de estado no está permitida por las reglas de tu flujo de trabajo.')
                ->danger()
                ->send();
        }
    }

    public function updateTicketAssignee($ticketId, $userId): void
    {
        $ticket = Ticket::find($ticketId);

        if (! $ticket) {
            return;
        }

        $ticket->assigned_to = $userId ?: null;
        $ticket->save();

        Notification::make()
            ->title('Asignación actualizada')
            ->body("El responsable de #{$ticket->ticket_number} fue cambiado exitosamente.")
            ->success()
            ->send();
    }
}
