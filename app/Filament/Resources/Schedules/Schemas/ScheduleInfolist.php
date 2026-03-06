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

                                return Attendance::whereIn('schedule_class_id', $pivotIds)
                                    ->with('student') 
                                    ->get();
                            })
                            ->schema([
                                TextEntry::make('student.name')
                                    ->label('Nama Siswa')
                                    ->weight('bold'),

                                TextEntry::make('student.schoolClass.name')
                                    ->label('Nama Siswa')
                                    ->weight('bold'),
                                
                                TextEntry::make('created_at')
                                    ->label('Waktu')
                                    ->time('H:i'),
                                    
                                TextEntry::make('status') 
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'hadir' => 'success',
                                        'tidak_hadir' => 'danger',
                                        default => 'gray',
                                    }),
                            ])
                            ->grid(3)
                            ->columnSpanFull(),
                    ])
            ])->columns(1);
    }
}