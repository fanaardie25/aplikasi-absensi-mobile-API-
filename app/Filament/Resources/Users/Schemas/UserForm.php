<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\SchoolClass;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pribadi Siswa')
                    ->description('Informasi dasar dan akademik siswa.')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->columnSpanFull(), 

                        Grid::make(2)->schema([
                            TextInput::make('nis')
                                ->label('NIS')
                                ->required()
                                ->placeholder('Contoh: P.12345'),

                            Select::make('class_id')
                                ->required()
                                ->label('Kelas')
                                ->options(SchoolClass::query()->pluck('name', 'id'))
                                ->searchable()
                                ->native(false),

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
                    ->description('Pengaturan login aplikasi dan status akun.')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('email')
                                ->email()
                                ->required(),

                                TextInput::make('password')
                                ->password()
                                ->revealable()
                                ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                                ->dehydrated(fn ($state) => filled($state))
                                ->mutateDehydratedStateUsing(fn ($state) => Hash::make($state))
                                ->disabled(fn (Get $get) => $get('use_nis_as_password'))
                                ->placeholder(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord 
                                    ? 'Kosongkan jika tidak ingin mengubah password' 
                                    : 'Masukkan password'),
                        ]),

                        Grid::make(2)->schema([
                            Toggle::make('use_nis_as_password')
                                ->label('Gunakan NIS sebagai password')
                                ->live()
                                ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                    if ($state) {
                                        $set('password', $get('nis'));
                                    }
                                }),
                                
                            Toggle::make('is_active')
                                ->label('Akun Aktif')
                                ->default(true)
                                ->onColor('success')
                                ->offColor('danger')
                                ->helperText('Jika dinonaktifkan, siswa tidak bisa login.'),
                        ]),
                    ]),

                Section::make('Foto Profil')
                    ->description('Unggah foto.')
                    ->icon('heroicon-o-camera')
                    ->collapsed() 
                    ->schema([
                        FileUpload::make('profile_photo_path')
                            ->disk('public')
                            ->label('Unggah Foto')
                            ->directory('profile')
                            ->visibility('public')
                            ->image()
                            ->imageEditor() 
                            ->maxSize(2048)
                            ->helperText('Format: JPG/PNG, Maksimal 2MB')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
