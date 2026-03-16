<?php

namespace App\Filament\Exports;

use App\Models\Attendance;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class KehadiranExporter extends Exporter
{
    protected static ?string $model = Attendance::class;
    public function getFileName(Export $export): string
    {
        return "Laporan-Absensi-SATA" . now()->format('d-m-Y')." ".$export->getKey();
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('photo_path')
                ->label('Link Foto Bukti')
                ->formatStateUsing(function ($state) {
                    if (!$state || $state === 'null' || $state === '') {
                        return '-';
                    }

                    return asset('storage/' . $state);
                }),

            ExportColumn::make('student.name')
                ->label('Nama Siswa'),

            ExportColumn::make('student.nis')
                ->label('NIS'),

            ExportColumn::make('student.schoolClass.name')
                ->label('Kelas'),

            ExportColumn::make('created_at')
                ->label('Tanggal Absen')
                ->formatStateUsing(fn ($state) => $state->format('d/m/Y')),

            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                        'hadir' => 'H',
                        'tidak_hadir' => 'A',
                        'izin' => 'I',
                        'sakit' => 'S',
                        default => ucfirst($state),
                    }),

            ExportColumn::make('latitude')
                ->label('Lat'),
            ExportColumn::make('longtitude')
                ->label('Long'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data kehadiran telah selesai dan ' . Number::format($export->successful_rows) . ' data berhasil diunduh.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' data gagal diekspor.';
        }

        return $body;
    }
}
