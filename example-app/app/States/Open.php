<?php
namespace App\States;

class Open extends TicketState
{
    public static string $name = 'open';

    public function color(): string
    {
        return 'warning'; // 'success', 'danger', 'info', etc.
    }
}
