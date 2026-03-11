<?php

namespace App\Filament\Teacher\Resources\Attendances\Tables;

use App\Filament\Exports\KehadiranExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            ImageColumn::make('photo_path')
                ->disk('public')
                ->label('Bukti Foto')
                ->circular()
                ->extraImgAttributes(['loading' => 'lazy']),

            TextColumn::make('student.name')
                ->label('Nama Siswa')
                ->searchable()
                ->sortable()
                ->description(fn ($record) => "NIS: {$record->student->nis}"),


            TextColumn::make('class.name')
                ->label('Kelas')
                ->badge()
                ->color('gray'),

            TextColumn::make('created_at')
                ->label('Waktu Absen')
                ->dateTime('d M Y, H:i')
                ->sortable(),


            TextColumn::make('status')
                ->badge()
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'hadir' => 'Hadir',
                    'izin' => 'Izin',
                    'sakit' => 'sakit',
                    'tidak_hadir' => 'Alpa',
                    default => 'Tanpa Keterangan',
                })
                ->color(fn (string $state): string => match ($state) {
                    'hadir' => 'success',
                    'izin' => 'warning',
                    'sakit' => 'primary',
                    'tidak_hadir' => 'danger',
                    default => 'gray',
                }),

            TextColumn::make('latitude')
                ->label('Koordinat')
                ->icon('heroicon-m-map-pin')
                ->color('primary')
                ->formatStateUsing(fn () => 'Lihat Map')
                ->url(fn ($record) => "https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longtitude}")
                ->openUrlInNewTab(),
            ])
            ->filters([
                SelectFilter::make('status')
                ->options([
                    'hadir' => 'Hadir',
                    'tidak_hadir' => 'Alpa',
                    'izin' => 'izin',
                    'sakit' => 'sakit'
                ]),
                SelectFilter::make('class')
                    ->label('Kelas')
                    ->relationship('class', 'name', function ($query) {
                        return $query->where('teacher_id', Auth::id());
                    }),
                Filter::make('created_at')
                ->schema([
                    DatePicker::make('from'),
                    DatePicker::make('until'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
                        ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']));
                })
            ])
            ->headerActions([
                 ExportAction::make()
                    ->exporter(KehadiranExporter::class)
                    ->label('Unduh Laporan')
                    ->icon('heroicon-o-arrow-down-tray') 
                    ->color('success') 
                    ->columnMapping(false)
                    ->modalHeading('Ekspor Laporan Kehadiran')
                    ->modalDescription('File akan diunduh dalam format XLSX. Pastikan filter sudah sesuai sebelum mengunduh.')
                    ->modalSubmitActionLabel('Mulai Ekspor')
            ])
            ->recordActions([
            ])
            ->toolbarActions([
            ]);
    }
}
