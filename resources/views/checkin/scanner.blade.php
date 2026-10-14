<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner Check-in — {{ $event->title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- html5-qrcode via CDN (FR-09) — butuh HTTPS di production --}}
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body class="min-h-screen bg-gray-900 text-white">

<div class="max-w-sm mx-auto px-4 py-6 space-y-4">

    <div class="text-center">
        <p class="text-xs uppercase tracking-widest text-gray-400">Check-in Scanner</p>
        <h1 class="text-lg font-bold mt-1">{{ $event->title }}</h1>
        <p class="text-sm text-gray-400">{{ $event->starts_at->isoFormat('D MMMM YYYY') }} · {{ $event->venue }}</p>
    </div>

    {{-- Area kamera --}}
    <div id="qr-reader" class="rounded-2xl overflow-hidden bg-black"></div>

    {{-- Hasil scan --}}
    <div id="result-box" class="hidden rounded-2xl p-5 text-center transition-all">
        <p id="result-icon" class="text-5xl mb-2"></p>
        <p id="result-name" class="text-xl font-bold"></p>
        <p id="result-message" class="text-sm mt-1 opacity-80"></p>
    </div>

    {{-- Status petugas --}}
    <p class="text-center text-xs text-gray-500">
        Masuk sebagai: <span class="text-gray-300">{{ auth()->user()->name }}</span>
    </p>

</div>

<script>
    const VERIFY_URL = "{{ route('checkin.verify', $event) }}";
    const CSRF      = document.querySelector('meta[name="csrf-token"]').content;

    let scanning = true;

    const resultBox  = document.getElementById('result-box');
    const resultIcon = document.getElementById('result-icon');
    const resultName = document.getElementById('result-name');
    const resultMsg  = document.getElementById('result-message');

    function showResult(result, icon, name, message) {
        resultBox.classList.remove('hidden', 'bg-green-800', 'bg-red-800', 'bg-yellow-800');
        const colorMap = { success: 'bg-green-800', duplicate: 'bg-yellow-800', invalid: 'bg-red-800', cancelled: 'bg-red-800' };
        resultBox.classList.add(colorMap[result] ?? 'bg-gray-700');
        resultIcon.textContent    = icon;
        resultName.textContent    = name;
        resultMsg.textContent     = message;

        // Resume scanner setelah 3 detik
        setTimeout(() => {
            resultBox.classList.add('hidden');
            scanning = true;
        }, 3000);
    }

    async function onScan(decodedText) {
        if (! scanning) return;
        scanning = false;

        // Ambil token dari URL yang di-encode ke QR
        let token = decodedText;
        try {
            const url = new URL(decodedText);
            token = url.searchParams.get('token') ?? decodedText;
        } catch (_) { /* bukan URL — pakai raw */ }

        try {
            const res  = await fetch(VERIFY_URL, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body:    JSON.stringify({ token }),
            });
            const data = await res.json();

            const icons = { success: '✅', duplicate: '⚠️', invalid: '❌', cancelled: '🚫' };
            showResult(data.result, icons[data.result] ?? '❓', data.name ?? '', data.message);
        } catch (e) {
            showResult('invalid', '❌', '', 'Gagal terhubung ke server.');
        }
    }

    const html5QrCode = new Html5Qrcode('qr-reader');
    html5QrCode.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        onScan,
        () => {}
    ).catch(err => {
        document.getElementById('qr-reader').innerHTML =
            '<p class="text-red-400 text-sm p-4 text-center">Tidak bisa mengakses kamera.<br>Pastikan halaman dibuka via HTTPS dan izin kamera diberikan.</p>';
    });
</script>

</body>
</html>
