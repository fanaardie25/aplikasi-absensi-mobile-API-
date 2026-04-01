<?php

namespace App\Filament\Resources\Agendas\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AgendaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Agenda')
                    ->placeholder('Contoh: Sholat Dzuhur, Kebersihan Masjid')
                    ->required(),
                TimePicker::make('start_absensi')
                    ->label('Jam Mulai Absensi')
                    ->required(),
                TimePicker::make('end_absensi')
                    ->label('Jam Batas Akhir Absensi')
                    ->required(),
                Select::make('teacher_id')
                    ->label('Guru Pengawas/Imam (Opsional)')
                    ->options(function () {
                        return User::where('role', 'teacher')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->nullable(),
                Select::make('category')
                    ->label('Kategori Agenda')
                    ->options([
                        'ibadah' => 'Ibadah',
                        'kebersihan' => 'Kebersihan',
                    ])
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->onColor('success')
                    ->offColor('danger'),
            ]);
    }
}
