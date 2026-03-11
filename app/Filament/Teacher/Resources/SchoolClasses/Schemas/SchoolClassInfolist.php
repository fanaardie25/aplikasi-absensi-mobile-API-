<?php

namespace App\Filament\Teacher\Resources\SchoolClasses\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SchoolClassInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
              Section::make('Daftar Siswa Terdaftar')
                    ->description('Siswa yang ada di kelas ini.')
                    ->schema([
                        RepeatableEntry::make('students') 
                            ->schema([
                                ImageEntry::make('profile_photo_path')
                                    ->label('foto')
                                    ->circular()
                                    ->disk('public')
                                    ->defaultImageUrl(fn ($record) => "https://ui-avatars.com/api/?name=" . urlencode($record->name) . "&background=10B981&color=fff")
                                    ->imageSize(40)
                                    ->extraImgAttributes(['loading' => 'lazy']),

                                TextEntry::make('name')
                                    ->label('Nama')
                                    ->weight('bold')
                                    ->grow(), 
                                TextEntry::make('email')
                                    ->label('Email/NIS')
                                    ->icon('heroicon-m-envelope')
                                    ->color('gray'),

                                TextEntry::make('is_active')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'gray')
                                    ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Non-Aktif'),
                            ])
                            ->columns(4) 
                            ->grid(1) 
                            ->columnSpanFull(),
                    ])
            ])->columns(1);
    }
}
