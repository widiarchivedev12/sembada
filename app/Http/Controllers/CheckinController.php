<?php

namespace App\Http\Controllers;

use App\Models\CheckinLog;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckinController extends Controller
{
    public function show(Event $event): View
    {
        return view('checkin.scanner', compact('event'));
    }

    /**
     * FR-09/10/11: Validasi token, update atomik, catat log.
     */
    public function verify(Request $request, Event $event): JsonResponse
    {
        $token = $request->string('token')->toString();
        $scannedBy = $request->user()->id;
        $now = now();

        // Cari tiket berdasarkan token
        $ticket = Ticket::whereHas('registration', fn ($q) => $q->where('event_id', $event->id))
            ->where('token', $token)
            ->first();

        if (! $ticket) {
            CheckinLog::create([
                'ticket_id' => null,
                'event_id' => $event->id,
                'scanned_by' => $scannedBy,
                'result' => 'invalid',
                'token_attempted' => $token,
                'scanned_at' => $now,
            ]);

            return response()->json([
                'result' => 'invalid',
                'message' => 'Token tidak dikenal.',
            ], 422);
        }

        if ($ticket->status === 'cancelled') {
            CheckinLog::create([
                'ticket_id' => $ticket->id,
                'event_id' => $event->id,
                'scanned_by' => $scannedBy,
                'result' => 'cancelled',
                'token_attempted' => $token,
                'scanned_at' => $now,
            ]);

            return response()->json([
                'result' => 'cancelled',
                'message' => 'Tiket ini sudah dibatalkan.',
            ], 422);
        }

        // FR-10: Update atomik — hanya berhasil jika status masih 'active'
        $updated = Ticket::where('id', $ticket->id)
            ->where('status', 'active')
            ->update([
                'status' => 'used',
                'checked_in_at' => $now,
                'checked_in_by' => $scannedBy,
            ]);

        if ($updated === 0) {
            // Baris tidak terpengaruh → tiket sudah dipakai (duplicate scan)
            CheckinLog::create([
                'ticket_id' => $ticket->id,
                'event_id' => $event->id,
                'scanned_by' => $scannedBy,
                'result' => 'duplicate',
                'token_attempted' => $token,
                'scanned_at' => $now,
            ]);

            return response()->json([
                'result' => 'duplicate',
                'message' => 'Tiket ini sudah digunakan sebelumnya.',
                'name' => $ticket->registration->name,
                'checked_in_at' => $ticket->checked_in_at?->format('H:i:s'),
            ], 422);
        }

        // FR-11: Log sukses
        CheckinLog::create([
            'ticket_id' => $ticket->id,
            'event_id' => $event->id,
            'scanned_by' => $scannedBy,
            'result' => 'success',
            'token_attempted' => $token,
            'scanned_at' => $now,
        ]);

        return response()->json([
            'result' => 'success',
            'message' => 'Check-in berhasil!',
            'name' => $ticket->registration->name,
            'code' => $ticket->code,
        ]);
    }
}
