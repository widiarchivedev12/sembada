<?php

namespace App\Http\Controllers;

use App\Exports\RegistrationsExport;
use App\Models\Event;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function registrations(Event $event): BinaryFileResponse
    {
        $user = auth()->user();

        // Spatie Gate::before intercepts named permissions before Policy runs,
        // so ownership must be enforced manually here.
        abort_unless($user->can('export reports'), 403);
        abort_if(! $user->can('view all events') && $event->created_by !== $user->id, 403);

        $filename = 'rekap-'.str($event->slug)->slug().'-'.now()->format('Ymd-His').'.xlsx';

        return Excel::download(new RegistrationsExport($event), $filename);
    }
}
