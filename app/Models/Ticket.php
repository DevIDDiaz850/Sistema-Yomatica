<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\ModelStates\HasStates;
use App\States\TicketState;


class Ticket extends Model
{
    use HasStates;

    protected $fillable = [
        'ticket_number',
        'title',
        'priority',
        'status',
        'assigned_to',
        'created_by',
        'description',
    ];

    protected $casts = [
        'status' => TicketState::class,
    ];

    protected static function booted(): void
    {
        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TCK-' . strtoupper(Str::random(5));
            }

            if (empty($ticket->created_by) && Auth::check()) {
                $ticket->created_by = Auth::id();
            }
        });
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
