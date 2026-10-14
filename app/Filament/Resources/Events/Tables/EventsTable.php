<?php

namespace App\Filament\Resources\Events\Tables;

use App\Models\Event;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('venue')
                    ->label('Lokasi')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('starts_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('quota')
                    ->label('Kuota')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('registrations_count')
                    ->label('Pendaftar')
                    ->counts('registrations')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'success',
                        'closed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'open' => 'Open',
                        'closed' => 'Closed',
                        default => $state,
                    }),

                TextColumn::make('creator.name')
                    ->label('Dibuat oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('starts_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'open' => 'Open',
                        'closed' => 'Closed',
                    ]),
            ])
            ->recordActions([
                Action::make('dashboard')
                    ->label('Dashboard')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->url(fn (Event $record): string => route('events.dashboard', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Event $record): bool => $record->status !== 'draft'
                        && (auth()->user()?->can('view registrations') ?? false)
                    ),

                Action::make('scanner')
                    ->label('Scanner')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->url(fn (Event $record): string => route('checkin.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (): bool => auth()->user()?->can('checkin tickets') ?? false),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
