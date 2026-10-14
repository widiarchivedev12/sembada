<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        return view('dashboard.show', compact('event'));
    }
}
