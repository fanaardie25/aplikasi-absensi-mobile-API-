<?php

namespace App\Filament\Resources\Holidays\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HolidayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Libur')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('date')
                    ->label('Tanggal Libur')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                Textarea::make('description')
                    ->label('Keterangan')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }
}
