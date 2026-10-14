<div>
    @if ($submitted)
        {{-- Sukses --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 text-center space-y-4">
            <div class="flex justify-center">
                <x-heroicon-o-check-circle class="w-16 h-16 text-green-500" />
            </div>
            <h2 class="text-xl font-bold text-gray-900">Pendaftaran Berhasil!</h2>
            <p class="text-gray-600">
                Tiket sudah dikirim ke <strong>{{ $email }}</strong>.<br>
                Simpan link tiket Anda:
            </p>
            <a
                href="{{ route('tickets.show', $ticketToken) }}"
                class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl transition"
            >
                Lihat Tiket Saya
            </a>
        </div>
    @else
        {{-- Form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Daftar Sekarang</h2>

            <form wire:submit="submit" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input
                        wire:model="name"
                        id="name"
                        type="text"
                        autocomplete="name"
                        placeholder="Nama Anda"
                        @class([
                            'w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition',
                            'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500' => ! $errors->has('name'),
                            'border-red-400 ring-1 ring-red-400 bg-red-50 focus:border-red-500 focus:ring-2 focus:ring-red-300' => $errors->has('name'),
                        ])
                    >
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input
                        wire:model="email"
                        id="email"
                        type="email"
                        autocomplete="email"
                        placeholder="email@contoh.com"
                        @class([
                            'w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition',
                            'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500' => ! $errors->has('email'),
                            'border-red-400 ring-1 ring-red-400 bg-red-50 focus:border-red-500 focus:ring-2 focus:ring-red-300' => $errors->has('email'),
                        ])
                    >
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                        <input
                            wire:model="phone"
                            id="phone"
                            type="tel"
                            autocomplete="tel"
                            placeholder="08xxxxxxxxxx"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                        >
                    </div>

                    <div>
                        <label for="institution" class="block text-sm font-medium text-gray-700 mb-1">Instansi / NIM / Fakultas</label>
                        <input
                            wire:model="institution"
                            id="institution"
                            type="text"
                            placeholder="Opsional"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-semibold py-3 rounded-xl transition"
                >
                    <span wire:loading.remove>Daftar Sekarang</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </form>
        </div>
    @endif
</div>
