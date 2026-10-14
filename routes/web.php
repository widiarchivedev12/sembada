<?php

use App\Http\Controllers\CheckinController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExportController;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $events = Event::where('status', 'open')
        ->orderBy('starts_at')
        ->get();

    return view('welcome', compact('events'));
});

Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

// FR-08: halaman tiket peserta via token
Route::get('/tickets/{token}', function (string $token) {
    $ticket = Ticket::where('token', $token)->with('registration.event')->firstOrFail();

    return view('tickets.show', compact('ticket'));
})->name('tickets.show');

// FR-09/10/11: scanner check-in (butuh login)
// FR-12/13/14: dashboard & export (butuh login + otorisasi via policy)
Route::middleware('auth')->group(function () {
    Route::get('/checkin/{event:slug}', [CheckinController::class, 'show'])
        ->name('checkin.show')
        ->middleware('permission:checkin tickets');
    Route::post('/checkin/{event:slug}/verify', [CheckinController::class, 'verify'])
        ->name('checkin.verify')
        ->middleware(['permission:checkin tickets', 'throttle:120,1']);

    Route::get('/events/{event:slug}/dashboard', [DashboardController::class, 'show'])->name('events.dashboard');
    Route::get('/events/{event:slug}/export', [ExportController::class, 'registrations'])->name('events.export');
});
