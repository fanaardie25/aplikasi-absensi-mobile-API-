<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
               DatePicker::make('date')
                ->label('Tanggal Jadwal Jumat')
                ->format('Y/m/d')
                ->native(false)
                ->displayFormat('d/m/Y') 
                ->required()
                
                ->unique('friday_schedules', 'date', ignoreRecord: true)
                
                ->afterOrEqual(today()) 
                
                ->rules([
                    fn (): Closure => function (string $attribute, $value, Closure $fail) {
                        $date = Carbon::parse($value);
                        if ($date->dayOfWeek !== Carbon::FRIDAY) {
                            $fail('Pembuatan     jadwal gagal. Tanggal yang dipilih harus jatuh pada hari Jumat.');
                        }
                    },
                ])
                
                ->closeOnDateSelection(),
                TextInput::make('description'),
                Select::make('classes')
                ->label('Pilih Kelas')
                ->multiple() 
                ->relationship('classes', 'name')
                ->preload() 
                ->searchable()
                ->required(),
                Select::make('teacher_id') 
                    ->relationship('teachers', 'name', function ($query) {
                        return $query->where('role','teacher'); 
                    })
                    ->label('Pilih Imam dan khatib')
                    ->preload() 
                    ->searchable(),
            ]);
    }
}
