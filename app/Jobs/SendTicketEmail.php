<?php

namespace App\Jobs;

use App\Mail\TicketMail;
use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendTicketEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly Ticket $ticket) {}

    public function handle(): void
    {
        $this->ticket->loadMissing('registration.event');

        Mail::to($this->ticket->registration->email)
            ->send(new TicketMail($this->ticket));
    }
}
