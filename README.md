# Sembada

> *Daftar, scan, hadir.*

Sistem manajemen event dan tiket kampus berbasis web. Peserta mendaftar tanpa akun, menerima tiket ber-QR lewat email, dan panitia mencatat kehadiran dengan memindai QR di pintu masuk — tanpa kertas, tanpa tiket ganda, rekap siap saat acara selesai.

## Tech Stack

- **Laravel 12** · PHP 8.3+
- **SQLite** (WAL mode) — satu file, tanpa server database terpisah
- **UUID v7** untuk semua primary key
- **Filament v4** — panel admin di `/admin`
- **Livewire 3** — form pendaftaran, dashboard real-time, check-in manual
- **Spatie laravel-permission** — role & permission (admin, panitia, petugas)
- **simplesoftwareio/simple-qrcode** — generate QR server-side (SVG)
- **html5-qrcode** — scanner kamera di browser
- **maatwebsite/excel** — ekspor rekap CSV/Excel

## Fitur

| Alur | Keterangan |
|---|---|
| Pendaftaran publik | Form tanpa akun di `/events/{slug}` |
| Kuota otomatis | Pendaftaran ditolak saat penuh (atomic, race-condition safe) |
| Tiket QR via email | Dikirim via queue, retry 3× jika gagal |
| Halaman tiket | Link bertoken di `/tickets/{token}` |
| Scanner check-in | Kamera HP, validasi token, anti tiket ganda |
| Log check-in | Setiap scan (sukses/gagal) tercatat dengan petugas & waktu |
| Dashboard real-time | Statistik kehadiran diperbarui tiap 5 detik |
| Check-in manual | Cari peserta by nama/email tanpa QR |
| Ekspor rekap | CSV/Excel lengkap dengan status hadir |
| Batalkan tiket | Panitia bisa batalkan tiket dari panel admin |

## Peran Pengguna

| Peran | Akses |
|---|---|
| **Admin** | Semua event, semua user, semua fitur panel |
| **Panitia** | Event milik sendiri: CRUD, daftar peserta, ekspor, batalkan tiket |
| **Petugas** | Scanner check-in & check-in manual saja |
| **Peserta** | Daftar & buka tiket (tanpa akun) |

## Instalasi

```bash
git clone <repo-url> sembada
cd sembada

cp .env.example .env
composer install
npm install && npm run build

php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

Seed membuat tiga akun awal:

| Email | Password | Peran |
|---|---|---|
| admin@sembada.test | password | Admin |
| panitia@sembada.test | password | Panitia |
| petugas@sembada.test | password | Petugas |

## Jalankan

```bash
# Development
php artisan serve
php artisan queue:work   # di terminal terpisah

# Atau sekaligus
composer run dev
```

Akses panel admin di `http://localhost:8000/admin`.

## Konfigurasi Email

Atur variabel SMTP di `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=noreply@sembada.test
MAIL_FROM_NAME=Sembada
```

Untuk development, gunakan `MAIL_MAILER=log` agar email masuk ke `storage/logs/laravel.log`.

## Struktur Route Utama

```
GET  /events/{slug}                  Halaman publik event
GET  /tickets/{token}                Halaman tiket peserta
GET  /checkin/{event}                Scanner check-in (auth)
POST /checkin/{event}/verify         Validasi token (auth, throttle)
GET  /events/{event}/dashboard       Dashboard kehadiran (auth)
GET  /events/{event}/export          Ekspor rekap Excel (auth)
     /admin                          Panel Filament (auth)
```

## Lisensi

MIT
