<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->helperText(fn (string $operation): string => $operation === 'edit'
                                ? 'Kosongkan jika tidak ingin mengubah password.'
                                : ''),

                        Select::make('role')
                            ->label('Peran')
                            ->options([
                                'admin' => 'Admin',
                                'panitia' => 'Panitia',
                                'petugas' => 'Petugas',
                            ])
                            ->required()
                            ->native(false)
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
