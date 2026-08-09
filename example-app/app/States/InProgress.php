<?php
namespace App\States;

class InProgress extends TicketState
{
    public static string $name = 'in_progress';

    public function color(): string { return 'info'; }
}
