<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\SchoolClass;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('nis')
                    ->required()
                    ->placeholder('Contoh: P.12345'),
                TextInput::make('email')
                    ->email()
                    ->required(),
                Toggle::make('use_nis_as_password')
                ->label('Gunakan NIS sebagai password')
                ->live()
                ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                    if ($state) {
                        $set('password', $get('nis'));
                    }
                }),

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
                Select::make('class_id')
                    ->required()
                    ->label('Kelas')
                    ->options(SchoolClass::query()->pluck('name', 'id'))
                    ->searchable()
                    ->native(false),
                FileUpload::make('profile_photo_path')
                    ->disk('public')
                    ->label('Foto Profil')
                    ->directory('profile')
                    ->visibility('public')
                    ->image()
                    ->imageEditor() 
                    ->maxSize(1024)
                    ->helperText('Format: JPG/PNG, Maksimal 1MB')
            ]);
    }
}
