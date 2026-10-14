<x-layouts.app :title="'Dashboard — ' . $event->title">

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <p class="text-xs uppercase tracking-widest text-gray-400">Dashboard Kehadiran</p>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $event->title }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $event->starts_at->isoFormat('D MMMM YYYY, HH:mm') }} · {{ $event->venue }}
                </p>
            </div>

            @can('export reports', $event)
                <a
                    href="{{ route('events.export', $event) }}"
                    class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-xl shadow-sm transition"
                >
                    ⬇ Ekspor Rekap
                </a>
            @endcan
        </div>

        {{-- Stats real-time (FR-12) --}}
        <livewire:attendance-dashboard :event="$event" />

        {{-- Check-in manual (FR-14) --}}
        @can('checkin tickets')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Check-in Manual</h2>
                <livewire:manual-checkin :event="$event" />
            </div>
        @endcan

    </div>

</x-layouts.app>
