<?php

namespace App\Filament\Resources\Agendas\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AgendaForm
{
    public static function configure(Schema $schema): Schema
    {
return $schema
            ->components([
                // SECTION 1: Informasi Utama
                Section::make('Informasi Agenda')
                    ->description('Detail dasar mengenai kegiatan yang akan dilaksanakan.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Agenda')
                            ->placeholder('Contoh: Sholat Dzuhur, Kebersihan Masjid')
                            ->required()
                            ->columnSpanFull(), 

                        Grid::make(2)->schema([
                            Select::make('category')
                                ->label('Kategori Agenda')
                                ->options([
                                    'ibadah' => 'Ibadah',
                                    'kebersihan' => 'Kebersihan',
                                ])
                                ->live() 
                                ->required(),

                            Select::make('teacher_id')
                                ->label('Guru Pengawas/Imam (Opsional)')
                                ->options(function () {
                                    return User::where('role', 'teacher')->where('is_active', true)->pluck('name', 'id');
                                })
                                ->searchable()
                                ->nullable(),
                        ]),
                    ]),

                // SECTION 2: Pengaturan Waktu
                Section::make('Waktu Pelaksanaan')
                    ->description('Tentukan rentang waktu santri dapat melakukan absen.')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(2)->schema([
                            TimePicker::make('start_absensi')
                                ->label('Jam Mulai Absensi')
                                ->required(),

                            TimePicker::make('end_absensi')
                                ->label('Jam Batas Akhir Absensi')
                                ->required(),
                        ]),
                    ]),

                // SECTION 3: Target Peserta
                Section::make('Target Peserta')
                    ->description('Filter siapa saja yang diwajibkan hadir pada agenda ini.')
                    ->icon('heroicon-o-users')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('target_gender')
                                ->label('Target Peserta (Berdasarkan Jenis Kelamin)')
                                ->options([
                                    'ALL' => 'Umum (Laki-laki & Perempuan)',
                                    'L'   => 'Khusus Laki-laki',
                                    'P'   => 'Khusus Perempuan',
                                ])
                                ->default('ALL')
                                ->required(),

                            Select::make('target_religion')
                                ->label('Target Peserta (Berdasarkan Agama)')
                                ->options([
                                    'ALL'      => 'Umum (Semua Agama)',
                                    'Islam'    => 'Islam',
                                    'Kristen'  => 'Kristen',
                                    'Katolik'  => 'Katolik',
                                    'Hindu'    => 'Hindu',
                                    'Buddha'   => 'Buddha',
                                ])
                                ->default('ALL')
                                ->required(),
                        ]),
                    ]),

                // SECTION 4: Status
                Section::make('Status Agenda')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Aktifkan Agenda Ini')
                            ->default(true) // Otomatis nyala pas bikin baru
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
            ]);
    }
}
