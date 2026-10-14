<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Ticket $ticket) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tiket Anda: '.$this->ticket->registration->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ticket',
            with: [
                'ticket' => $this->ticket,
                'event' => $this->ticket->registration->event,
                'registration' => $this->ticket->registration,
                'ticketUrl' => route('tickets.show', $this->ticket->token),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
