<?php

namespace App\Livewire;

use App\Jobs\SendTicketEmail;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;

class RegistrationForm extends Component
{
    public Event $event;

    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('required|email|max:255')]
    public string $email = '';

    #[Rule('nullable|string|max:20')]
    public string $phone = '';

    #[Rule('nullable|string|max:255')]
    public string $institution = '';

    public bool $submitted = false;

    public ?string $ticketToken = null;

    private ?string $createdTicketId = null;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function submit(): void
    {
        $this->validate();

        if (! $this->event->isRegistrationOpen()) {
            $this->addError('email', 'Pendaftaran sudah ditutup atau kuota penuh.');

            return;
        }

        try {
            DB::transaction(function () {
                // Cek kuota di dalam transaksi (FR-05)
                $confirmed = Registration::where('event_id', $this->event->id)
                    ->where('status', 'confirmed')
                    ->count();

                if ($confirmed >= $this->event->quota) {
                    throw new \RuntimeException('Kuota sudah penuh.');
                }

                // Cek duplikasi email (FR-04)
                $exists = Registration::where('event_id', $this->event->id)
                    ->where('email', $this->email)
                    ->exists();

                if ($exists) {
                    throw new \RuntimeException('Email ini sudah terdaftar untuk event ini.');
                }

                $registration = Registration::create([
                    'event_id' => $this->event->id,
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone ?: null,
                    'institution' => $this->institution ?: null,
                    'status' => 'confirmed',
                ]);

                $ticket = Ticket::create([
                    'registration_id' => $registration->id,
                    'code' => 'EVT-'.strtoupper(Str::random(6)),
                    'token' => Str::random(40),
                    'status' => 'active',
                ]);

                $this->ticketToken = $ticket->token;
                $this->createdTicketId = $ticket->id;
            });

            $this->submitted = true;
            SendTicketEmail::dispatch(Ticket::find($this->createdTicketId));
        } catch (\RuntimeException $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.registration-form');
    }
}
