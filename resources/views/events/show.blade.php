<x-layouts.app :title="$event->title . ' — Sembada'">

    <div class="space-y-8">

        {{-- Header event --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <span @class([
                        'inline-block text-xs font-semibold px-2.5 py-1 rounded-full mb-3',
                        'bg-green-100 text-green-700' => $event->status === 'open',
                        'bg-red-100 text-red-700'   => $event->status === 'closed',
                        'bg-gray-100 text-gray-600'  => $event->status === 'draft',
                    ])>
                        {{ match($event->status) {
                            'open'   => 'Pendaftaran Dibuka',
                            'closed' => 'Ditutup',
                            default  => 'Draft',
                        } }}
                    </span>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                </div>
            </div>

            <dl class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">Tanggal</dt>
                    <dd class="font-medium mt-0.5">
                        {{ $event->starts_at->isoFormat('D MMMM YYYY, HH:mm') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Lokasi</dt>
                    <dd class="font-medium mt-0.5">{{ $event->venue }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Sisa Kuota</dt>
                    <dd class="font-medium mt-0.5">
                        @php
                            $registered = $event->registrations()->where('status', 'confirmed')->count();
                            $sisa = $event->quota - $registered;
                        @endphp
                        {{ max(0, $sisa) }} / {{ $event->quota }}
                    </dd>
                </div>
            </dl>

            @if ($event->description)
                <p class="mt-5 text-gray-600 leading-relaxed whitespace-pre-line">{{ $event->description }}</p>
            @endif
        </div>

        {{-- Form pendaftaran --}}
        @if ($event->isRegistrationOpen())
            <livewire:registration-form :event="$event" />
        @else
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center text-amber-800">
                <p class="font-medium">Pendaftaran sudah ditutup atau kuota telah penuh.</p>
            </div>
        @endif

    </div>

</x-layouts.app>
