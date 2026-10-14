<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Event')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create'
                                ? $set('slug', Str::slug($state))
                                : null
                            )
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('URL event: /events/{slug}'),

                        TextInput::make('venue')
                            ->label('Lokasi')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),

                Section::make('Waktu & Kuota')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label('Mulai')
                            ->required()
                            ->native(false),

                        DateTimePicker::make('ends_at')
                            ->label('Selesai')
                            ->required()
                            ->native(false)
                            ->after('starts_at'),

                        DateTimePicker::make('registration_closes_at')
                            ->label('Batas Pendaftaran')
                            ->native(false)
                            ->before('starts_at'),

                        TextInput::make('quota')
                            ->label('Kuota')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->integer(),
                    ]),

                Section::make('Status')
                    ->schema([
                        Select::make('status')
                            ->label('Status Event')
                            ->options([
                                'draft' => 'Draft',
                                'open' => 'Open (Pendaftaran Dibuka)',
                                'closed' => 'Closed',
                            ])
                            ->default('draft')
                            ->required(),
                    ]),
            ]);
    }
}
