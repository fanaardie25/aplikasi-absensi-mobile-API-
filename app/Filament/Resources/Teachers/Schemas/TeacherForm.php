<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('nip')
                    ->label('NIP')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                Checkbox::make('use_default_password')
                    ->label('Gunakan Password Default (guru123!)')
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        if ($state) {
                            $set('password', 'guru123!');
                        } else {
                            $set('password', '');
                        }
                    })
                    ->visible(fn (string $context): bool => $context === 'create')
                    ->dehydrated(false),

                TextInput::make('password')
                    ->password()
                    ->revealable() 
                    ->disabled(fn (Get $get): bool => $get('use_default_password'))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context, Get $get): bool => 
                        $context === 'create' && !$get('use_default_password')
                    )
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                Hidden::make('role')
                    ->default('teacher'),
            ]);
    }
}
