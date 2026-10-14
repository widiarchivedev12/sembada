<?php

namespace App\Livewire;

use App\Models\CheckinLog;
use App\Models\Event;
use Livewire\Attributes\Poll;
use Livewire\Component;

class AttendanceDashboard extends Component
{
    public Event $event;

    #[Poll(5000)]
    public function render(): \Illuminate\View\View
    {
        $totalRegistered = $this->event->registrations()
            ->where('status', 'confirmed')
            ->count();

        $totalAttended = $this->event->registrations()
            ->where('status', 'confirmed')
            ->whereHas('ticket', fn ($q) => $q->where('status', 'used'))
            ->count();

        $percentage = $totalRegistered > 0
            ? round(($totalAttended / $totalRegistered) * 100)
            : 0;

        $recentLogs = CheckinLog::where('event_id', $this->event->id)
            ->where('result', 'success')
            ->with('ticket.registration')
            ->orderByDesc('scanned_at')
            ->limit(10)
            ->get();

        return view('livewire.attendance-dashboard', compact(
            'totalRegistered',
            'totalAttended',
            'percentage',
            'recentLogs',
        ));
    }
}
