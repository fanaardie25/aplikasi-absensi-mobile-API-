<?php

namespace App\Filament\Resources\Holidays;

use App\Filament\Resources\Holidays\Pages\ListHolidays;
use App\Models\Holiday;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDateRange;

    protected static string|UnitEnum|null $navigationGroup = "Akademik";
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = "Hari Libur";
    protected static ?string $modelLabel = 'Hari Libur';
    protected static ?string $pluralModelLabel = 'Hari Libur';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHolidays::route('/'),
        ];
    }
}
