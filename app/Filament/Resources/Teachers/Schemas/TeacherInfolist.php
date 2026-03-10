<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Guru')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->label('Nama Lengkap'),
                    TextEntry::make('nip')->label('NIP'),
                    TextEntry::make('email')->label('Email'),
                ]),

            Section::make('Daftar Kelas Bimbingan')
                ->description('Kelas yang menjadi tanggung jawab Anda pada tahun ajaran ini.')
                ->schema([
                    RepeatableEntry::make('supervisedClasses')
                        ->label('kelas Bimbingan')
                        ->grid(3)
                        ->schema([
                            TextEntry::make('name')
                                ->label('Nama Kelas')
                                ->weight('bold')
                                ->color('primary'),
                            TextEntry::make('academicYear.year')
                                ->label('Tahun Ajaran'),
                            TextEntry::make('students_count')
                                ->label('Jumlah Siswa')
                                ->state(fn ($record) => $record->students()->count() . ' Siswa'),
                        ])
                ])->visible(fn ($record) => $record->role === 'teacher'),
            ]);
    }
}
