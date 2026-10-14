<?php

namespace App\Livewire;

use App\Models\CheckinLog;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Ticket;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ManualCheckin extends Component
{
    public Event $event;

    #[Rule('required|string|min:2')]
    public string $search = '';

    /** @var array<int, array<string, mixed>> */
    public array $results = [];

    public ?string $checkedInName = null;

    public function searchRegistrations(): void
    {
        $this->validate();

        $this->results = $this->event->registrations()
            ->with('ticket')
            ->where(fn ($q) => $q
                ->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
            )
            ->where('status', 'confirmed')
            ->limit(10)
            ->get()
            ->map(fn (Registration $r) => [
                'id'         => $r->id,
                'name'       => $r->name,
                'email'      => $r->email,
                'institution'=> $r->institution,
                'ticket_id'  => $r->ticket?->id,
                'ticket_status' => $r->ticket?->status,
                'code'       => $r->ticket?->code,
            ])
            ->toArray();
    }

    public function checkIn(string $ticketId): void
    {
        $ticket = Ticket::where('id', $ticketId)
            ->whereHas('registration', fn ($q) => $q->where('event_id', $this->event->id))
            ->firstOrFail();

        $now = now();

        // Atomik — sama seperti scanner QR (FR-10)
        $updated = Ticket::where('id', $ticket->id)
            ->where('status', 'active')
            ->update([
                'status'        => 'used',
                'checked_in_at' => $now,
                'checked_in_by' => auth()->id(),
            ]);

        $result = $updated > 0 ? 'success' : 'duplicate';

        CheckinLog::create([
            'ticket_id'       => $ticket->id,
            'event_id'        => $this->event->id,
            'scanned_by'      => auth()->id(),
            'result'          => $result,
            'token_attempted' => null,
            'scanned_at'      => $now,
        ]);

        if ($result === 'success') {
            $this->checkedInName = $ticket->registration->name;
        }

        $this->searchRegistrations();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.manual-checkin');
    }
}
