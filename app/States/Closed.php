<?php
namespace App\States;

class Closed extends TicketState
{
    public static string $name = 'closed';

    public function color(): string { return 'success'; }
}
