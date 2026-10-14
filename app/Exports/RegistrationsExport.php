<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RegistrationsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(private readonly Event $event) {}

    public function query()
    {
        return $this->event->registrations()
            ->with('ticket')
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Email',
            'Telepon',
            'Instansi',
            'Status Pendaftaran',
            'Kode Tiket',
            'Status Tiket',
            'Waktu Check-in',
            'Waktu Daftar',
        ];
    }

    private int $row = 0;

    public function map($registration): array
    {
        $this->row++;

        return [
            $this->row,
            $registration->name,
            $registration->email,
            $registration->phone ?? '-',
            $registration->institution ?? '-',
            $registration->status === 'confirmed' ? 'Terdaftar' : 'Dibatalkan',
            $registration->ticket?->code ?? '-',
            match ($registration->ticket?->status) {
                'active'    => 'Aktif',
                'used'      => 'Hadir',
                'cancelled' => 'Dibatalkan',
                default     => '-',
            },
            $registration->ticket?->checked_in_at?->format('d/m/Y H:i:s') ?? '-',
            $registration->created_at->format('d/m/Y H:i:s'),
        ];
    }
}
