<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Título')
                    ->placeholder('Ej: Error al emitir factura')
                    ->required()
                    ->maxLength(255),

                Select::make('priority')
                    ->label('Prioridad')
                    ->options([
                        'low' => '🟢 Baja',
                        'medium' => '🔵 Media',
                        'high' => '🟠 Alta',
                        'urgent' => '🔴 Urgente',
                    ])
                    ->default('medium')
                    ->required(),

                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'open' => 'Abierto',
                        'in_progress' => 'En Progreso',
                        'resolved' => 'Resuelto',
                        'closed' => 'Cerrado',
                    ])
                    ->default('open')
                    ->required(),

                Select::make('assigned_to')
                    ->label('Asignado a')
                    ->relationship('assignee', 'name', fn ($query) => $query->whereIn('type', ['employee', 'admin']))
                    ->searchable()
                    ->preload(),

                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(4)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
