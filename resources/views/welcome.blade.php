<x-layouts.app title="Sembada — Sistem Tiket Event Kampus">

    {{-- Hero --}}
    <div class="text-center py-12">
        <h1 class="text-4xl font-bold text-gray-900 tracking-tight">Sembada</h1>
        <p class="mt-3 text-lg text-gray-500">Daftar, scan, hadir. Sistem tiket event kampus.</p>
    </div>

    {{-- Event list --}}
    @if ($events->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <p class="text-lg">Belum ada event yang dibuka saat ini.</p>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($events as $event)
                <a
                    href="{{ route('events.show', $event->slug) }}"
                    class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-200 transition p-6 flex flex-col gap-3"
                >
                    <div class="flex items-start justify-between gap-2">
                        <h2 class="text-base font-semibold text-gray-900 group-hover:text-indigo-600 transition leading-snug">
                            {{ $event->title }}
                        </h2>
                        <span class="shrink-0 text-xs font-medium bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                            Open
                        </span>
                    </div>

                    <div class="space-y-1.5 text-sm text-gray-500">
                        <p class="flex items-center gap-1.5">
                            <x-heroicon-o-map-pin class="w-4 h-4 shrink-0" />
                            {{ $event->venue }}
                        </p>
                        <p class="flex items-center gap-1.5">
                            <x-heroicon-o-calendar class="w-4 h-4 shrink-0" />
                            {{ $event->starts_at->isoFormat('D MMMM YYYY, HH:mm') }}
                        </p>
                        @if (! $event->isQuotaFull())
                            <p class="flex items-center gap-1.5">
                                <x-heroicon-o-ticket class="w-4 h-4 shrink-0" />
                                Sisa kuota: {{ $event->quota - $event->confirmedRegistrationsCount() }}
                            </p>
                        @else
                            <p class="flex items-center gap-1.5 text-red-500 font-medium">
                                <x-heroicon-o-x-circle class="w-4 h-4 shrink-0" />
                                Kuota penuh
                            </p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <div class="mt-10 text-center">
        <a
            href="/admin"
            class="text-sm text-gray-400 hover:text-indigo-500 transition"
        >
            Masuk sebagai panitia / petugas →
        </a>
    </div>

</x-layouts.app>
