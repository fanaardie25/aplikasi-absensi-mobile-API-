<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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
                Section::make('Data Pribadi Guru')
                    ->description('Informasi dasar mengenai guru atau staf pengajar.')
                    ->icon('heroicon-o-academic-cap') 
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap & Gelar')
                            ->required()
                            ->columnSpanFull(),

                        Grid::make(2)->schema([
                            TextInput::make('nip')
                                ->label('NIP')
                                ->required()
                                ->placeholder('Contoh: 198001012005011001'),

                            Select::make('gender')
                                ->label('Jenis Kelamin')
                                ->options([
                                    'L' => 'Laki-laki',
                                    'P' => 'Perempuan',
                                ])
                                ->required(),

                            Select::make('religion')
                                ->label('Agama')
                                ->options([
                                    'Islam'   => 'Islam',
                                    'Kristen' => 'Kristen',
                                    'Katolik' => 'Katolik',
                                    'Hindu'   => 'Hindu',
                                    'Buddha'  => 'Buddha',
                                ])
                                ->default('Islam') 
                                ->required(),
                        ]),
                    ]),

                Section::make('Akun & Keamanan')
                    ->description('Pengaturan akses login aplikasi.')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->columnSpanFull(), 

                        Grid::make(2)->schema([
                            Toggle::make('use_default_password')
                                ->label('Gunakan Password Default (guru123!)')
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                    if ($state) {
                                        $set('password', 'guru123!');
                                    } else {
                                        $set('password', null); 
                                    }
                                })
                                ->visible(fn (string $context): bool => $context === 'create')
                                ->dehydrated(false),

                            TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->revealable() 
                                ->disabled(fn (Get $get): bool => (bool) $get('use_default_password'))
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn (string $context, Get $get): bool => 
                                    $context === 'create' && !$get('use_default_password')
                                )
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                ->placeholder(fn (string $context) => $context === 'edit' 
                                    ? 'Kosongkan jika tidak ingin mengubah password' 
                                    : 'Masukkan password manual'),
                        ]),
                        
                        Hidden::make('role')
                            ->default('teacher'),
                    ]),
            ]);
    }
}
