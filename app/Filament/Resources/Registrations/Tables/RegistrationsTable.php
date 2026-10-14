<?php

namespace App\Filament\Resources\Registrations\Tables;

use App\Models\Registration;
use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('institution')
                    ->label('Instansi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ticket.code')
                    ->label('Kode Tiket')
                    ->fontFamily('mono'),

                TextColumn::make('ticket.status')
                    ->label('Tiket')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'used' => 'success',
                        'cancelled' => 'danger',
                        'active' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'used' => 'Hadir',
                        'cancelled' => 'Dibatalkan',
                        default => '-',
                    }),

                TextColumn::make('ticket.checked_in_at')
                    ->label('Check-in')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Daftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('ticket_status')
                    ->label('Status Tiket')
                    ->relationship('ticket', 'status')
                    ->options([
                        'active' => 'Aktif',
                        'used' => 'Hadir',
                        'cancelled' => 'Dibatalkan',
                    ]),
            ])
            ->recordActions([
                // FR-15: batalkan tiket
                Action::make('cancel_ticket')
                    ->label('Batalkan Tiket')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Tiket')
                    ->modalDescription('Tiket peserta ini akan dibatalkan dan tidak bisa dipakai check-in. Lanjutkan?')
                    ->visible(fn (Registration $record): bool => $record->ticket?->status === 'active')
                    ->authorize(fn () => auth()->user()?->can('cancel tickets'))
                    ->action(function (Registration $record): void {
                        Ticket::where('id', $record->ticket->id)
                            ->where('status', 'active')
                            ->update(['status' => 'cancelled']);

                        $record->update(['status' => 'cancelled']);

                        Notification::make()
                            ->title('Tiket berhasil dibatalkan')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
