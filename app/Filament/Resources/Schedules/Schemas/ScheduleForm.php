<?php

namespace App\Filament\Resources\Schedules\Schemas;

use App\Models\Agenda;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
              Select::make('agenda_id')
                    ->label('Pilih Agenda / Kegiatan')
                    ->options(Agenda::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),

                DatePicker::make('date')
                    ->label('Tanggal Kegiatan')
                    ->default(now())
                    ->format('Y/m/d')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->required()
                    ->closeOnDateSelection(),

                Select::make('classes')
                    ->label('Pilih Kelas')
                    ->multiple() 
                    ->relationship('classes', 'name')
                    ->preload() 
                    ->searchable()
                    ->required(),

                Textarea::make('description')
                    ->label('Keterangan Tambahan'),
            ]);
    }
}
