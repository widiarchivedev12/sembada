<div class="space-y-4">

    @if ($checkedInName)
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-800">
            <x-heroicon-o-check-circle class="w-5 h-5 shrink-0 text-green-600" />
            <span><strong>{{ $checkedInName }}</strong> berhasil check-in.</span>
        </div>
    @endif

    {{-- Search form --}}
    <form wire:submit="searchRegistrations" class="flex gap-2">
        <input
            wire:model="search"
            type="search"
            placeholder="Cari nama atau email peserta..."
            @class([
                'flex-1 rounded-xl border px-4 py-2.5 text-sm outline-none transition',
                'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500' => ! $errors->has('search'),
                'border-red-400 ring-1 ring-red-400 bg-red-50' => $errors->has('search'),
            ])
        >
        <button
            type="submit"
            wire:loading.attr="disabled"
            class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition"
        >
            <span wire:loading.remove>Cari</span>
            <span wire:loading>...</span>
        </button>
    </form>

    @error('search') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

    {{-- Hasil pencarian --}}
    @if (count($results) > 0)
        <div class="divide-y divide-gray-100 border border-gray-100 rounded-xl overflow-hidden">
            @foreach ($results as $r)
                <div class="flex items-center justify-between px-4 py-3 bg-white gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $r['name'] }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $r['email'] }}@if($r['institution']) · {{ $r['institution'] }}@endif</p>
                    </div>

                    <div class="shrink-0">
                        @if ($r['ticket_status'] === 'used')
                            <span class="text-xs text-green-600 font-semibold">✓ Hadir</span>
                        @elseif ($r['ticket_status'] === 'cancelled')
                            <span class="text-xs text-red-500 font-semibold">Dibatalkan</span>
                        @elseif ($r['ticket_id'])
                            <button
                                wire:click="checkIn('{{ $r['ticket_id'] }}')"
                                wire:loading.attr="disabled"
                                class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition"
                            >
                                Check-in
                            </button>
                        @else
                            <span class="text-xs text-gray-400">Tidak ada tiket</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @elseif (strlen($search) > 0 && count($results) === 0)
        <p class="text-sm text-gray-500 text-center py-4">Peserta tidak ditemukan.</p>
    @endif

</div>
