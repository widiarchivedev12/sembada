<div>
    {{-- Stat cards --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-gray-900">{{ $totalRegistered }}</p>
            <p class="text-sm text-gray-500 mt-1">Terdaftar</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $totalAttended }}</p>
            <p class="text-sm text-gray-500 mt-1">Hadir</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-indigo-600">{{ $percentage }}%</p>
            <p class="text-sm text-gray-500 mt-1">Kehadiran</p>
        </div>
    </div>

    {{-- Progress bar --}}
    <div class="mt-4 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex justify-between text-xs text-gray-500 mb-2">
            <span>Kehadiran</span>
            <span>{{ $totalAttended }} / {{ $totalRegistered }}</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-3">
            <div
                class="bg-green-500 h-3 rounded-full transition-all duration-500"
                style="width: {{ $percentage }}%"
            ></div>
        </div>
    </div>

    {{-- Log terbaru --}}
    @if ($recentLogs->isNotEmpty())
        <div class="mt-4 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm font-semibold text-gray-700 mb-3">Check-in Terbaru</p>
            <ul class="space-y-2">
                @foreach ($recentLogs as $log)
                    <li class="flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-900">{{ $log->ticket->registration->name }}</span>
                        <span class="text-gray-400 text-xs">{{ $log->scanned_at->format('H:i:s') }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
