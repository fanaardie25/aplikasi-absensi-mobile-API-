<?php

namespace App\Filament\Resources\Attendances\Tables;

use App\Filament\Exports\KehadiranExporter;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

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
                ->defaultImageUrl(fn ($record) => "https://ui-avatars.com/api/?name=" . match ($record->status) {
                    'tidak_hadir' => 'A', 
                    'izin' => 'I',
                    'hadir' => 'H',
                    'sakit' => 'S',
                    default => '?', 
                } . "&background=10B981&color=fff")
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


            SelectColumn::make('status')
                ->options([
                    'hadir' => 'Hadir',
                    'sakit' => 'sakit',
                    'izin' => 'Izin',
                    'tidak_hadir' => 'Alpa',
                ])
                ->selectablePlaceholder(false),

            ToggleColumn::make('is_verified')
                    ->label('Disetujui')
                    ->sortable(),

            TextColumn::make('latitude')
                ->label('Koordinat')
                ->icon('heroicon-m-map-pin')
                ->color('primary')
                ->formatStateUsing(fn () => 'Lihat Map')
                ->url(fn ($record) => "https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longtitude}")
                ->openUrlInNewTab(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                ->options([
                    'hadir' => 'Hadir',
                    'izin' => 'Izin',
                    'sakit' => 'Sakit',
                    'tidak_hadir' => 'Alpa',
                ]),
                SelectFilter::make('Kelas')
                ->relationship('class','name'),
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
                // Action::make('export_pdf')
                //     ->label('Download PDF')
                //     ->icon('heroicon-o-document-arrow-down')
                //     ->color('danger')
                //     ->action(function ($livewire) {
                //         $records = $livewire->getFilteredTableQuery()->get();

                //         $records = $records->sortBy('student.schoolClass.name');
                //         $filters = $livewire->tableFilters;
                //         $startDate = $filters['created_at']['from'] ?? null;
                //         $endDate = $filters['created_at']['until'] ?? null;

                //         $pdf = Pdf::loadView('pdf.attendance', [
                //             'records' => $records,
                //             'startDate' => $startDate,
                //             'endDate' => $endDate
                //         ]);

                //         return response()->streamDownload(
                //             fn () => print($pdf->output()), 
                //             'rekap-presensi-' . now()->format('Y-m-d') . '.pdf'
                //         );
                //     }),
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
                Action::make('lihat_bukti')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    // Tombol ini HANYA muncul kalau statusnya izin atau sakit
                    ->visible(fn ($record) => in_array($record->status, ['izin', 'sakit']))
                    ->modalHeading('Detail Presensi')
                    ->modalWidth('md')
                    // Menghilangkan tombol submit karena ini cuma view
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->schema([
                        Section::make()
                            ->schema([
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'sakit' => 'warning',
                                        'izin' => 'info',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                                
                                TextEntry::make('reason')
                                    ->label('Keterangan / Alasan')
                                    ->default('Tidak ada keterangan yang dilampirkan.'),

                                ImageEntry::make('photo_path')
                                    ->label('Bukti')
                                    ->disk('public')
                                    ->imageWidth('100%')
                                    ->imageHeight(400)
                                    ->extraImgAttributes(['class' => 'rounded-xl object-contain border border-gray-200']),
                            ])
                    ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
