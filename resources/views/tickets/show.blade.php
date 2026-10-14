<x-layouts.app :title="'Tiket — ' . $ticket->registration->event->title">

    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center space-y-6">

            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-indigo-600 mb-1">Tiket Masuk</p>
                <h1 class="text-xl font-bold text-gray-900">{{ $ticket->registration->event->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $ticket->registration->event->starts_at->isoFormat('D MMMM YYYY, HH:mm') }}
                    · {{ $ticket->registration->event->venue }}
                </p>
            </div>

            {{-- QR Code (FR-06) --}}
            @if ($ticket->status === 'active')
                <div class="flex justify-center">
                    {!! SimpleSoftware\QrCode\Facades\QrCode::size(200)->generate(
                        route('checkin.verify', ['token' => $ticket->token])
                    ) !!}
                </div>
            @else
                <div class="w-48 h-48 mx-auto bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 text-sm">
                    Tiket tidak aktif
                </div>
            @endif

            <div class="text-left space-y-2 border-t border-gray-100 pt-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Nama</span>
                    <span class="font-medium">{{ $ticket->registration->name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Email</span>
                    <span class="font-medium">{{ $ticket->registration->email }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Kode Tiket</span>
                    <span class="font-mono font-bold text-indigo-600">{{ $ticket->code }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Status</span>
                    <span @class([
                        'font-semibold',
                        'text-green-600' => $ticket->status === 'active',
                        'text-red-600'   => in_array($ticket->status, ['used', 'cancelled']),
                    ])>
                        {{ match($ticket->status) {
                            'active'    => 'Aktif',
                            'used'      => 'Sudah Digunakan',
                            'cancelled' => 'Dibatalkan',
                            default     => $ticket->status,
                        } }}
                    </span>
                </div>
            </div>

        </div>
    </div>

</x-layouts.app>
