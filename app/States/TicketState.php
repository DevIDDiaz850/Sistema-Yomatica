<?php
namespace App\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class TicketState extends State
{
    // Esto nos servirá para que el paquete de Filament pinte los colores
    abstract public function color(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Open::class)
            ->registerState(Open::class)
            ->registerState(InProgress::class)
            ->registerState(Closed::class)
            // AQUÍ DEFINES LAS REGLAS DE NEGOCIO ESTRICTAS
            ->allowTransition(Open::class, InProgress::class)
            ->allowTransition(InProgress::class, Closed::class)
            // Opcional: permitir reabrir un ticket
            ->allowTransition(Closed::class, Open::class);
    }
}
