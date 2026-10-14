<x-mail::message>
# Tiket Anda Sudah Siap, {{ $registration->name }}!

Terima kasih sudah mendaftar untuk:

**{{ $event->title }}**
{{ $event->starts_at->isoFormat('D MMMM YYYY, HH:mm') }} · {{ $event->venue }}

---

Kode tiket Anda: **{{ $ticket->code }}**

Tunjukkan QR code di bawah ini saat check-in di pintu masuk.

<x-mail::button :url="$ticketUrl">
Buka Tiket Saya
</x-mail::button>

Link tiket: {{ $ticketUrl }}

Simpan email ini atau bookmark link di atas agar tiket tidak hilang.

Sampai jumpa di acara!<br>
{{ config('app.name') }}
</x-mail::message>

