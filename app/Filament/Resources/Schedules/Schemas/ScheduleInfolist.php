<?php

namespace App\Filament\Resources\Schedules\Schemas;

use App\Models\Attendance;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema; 
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rekap Kehadiran')
                    ->description('Daftar siswa yang telah mencatatkan kehadiran pada jadwal ini.')
                    ->schema([
                        RepeatableEntry::make('custom_attendances') 
                            ->label('Daftar Siswa')
                            ->state(function ($record): Collection {
                               $pivotIds = DB::table('schedule_classes')
                                    ->where('schedule_id', $record->id)
                                    ->pluck('id'); 
                                    
                                $targetDate = $record->date;

                                return Attendance::whereIn('schedule_class_id', $pivotIds)
                                    ->with('student','student.schoolClass') 
                                    ->whereDate('created_at', $targetDate)
                                    ->get();
                            })
                            ->schema([
                                TextEntry::make('student.name')
                                    ->label('Nama Siswa')
                                    ->weight('bold'),

                                TextEntry::make('student.schoolClass.name')
                                    ->label('Kelas')
                                    ->weight('bold'),
                                
                                TextEntry::make('created_at')
                                    ->label('Waktu')
                                    ->time('H:i'),
                                    
                                TextEntry::make('status') 
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                            'hadir' => 'Hadir',
                                            'tidak_hadir' => 'Alpa',
                                            'izin' => 'Izin',
                                            'sakit' => 'Sakit',
                                            default => ucfirst($state),
                                        })
                                    ->color(fn (string $state): string => match ($state) {
                                        'hadir' => 'success',
                                        'tidak_hadir' => 'danger',
                                        'izin' => 'warning',
                                        default => 'gray',
                                    }),
                            ])
                            ->grid(3)
                            ->columnSpanFull(),
                    ])
            ])->columns(1);
    }
}