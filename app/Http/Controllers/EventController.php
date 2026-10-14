<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class EventController extends Controller
{
    public function show(string $slug): View
    {
        $event = Event::where('slug', $slug)
            ->whereIn('status', ['open', 'closed'])
            ->firstOrFail();

        return view('events.show', compact('event'));
    }
}
