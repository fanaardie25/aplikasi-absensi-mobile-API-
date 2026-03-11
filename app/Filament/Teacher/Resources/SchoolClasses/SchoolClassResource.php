<?php

namespace App\Filament\Teacher\Resources\SchoolClasses;

use App\Filament\Teacher\Resources\SchoolClasses\Pages\CreateSchoolClass;
use App\Filament\Teacher\Resources\SchoolClasses\Pages\EditSchoolClass;
use App\Filament\Teacher\Resources\SchoolClasses\Pages\ListSchoolClasses;
use App\Filament\Teacher\Resources\SchoolClasses\Pages\ViewSchoolClass;
use App\Filament\Teacher\Resources\SchoolClasses\Schemas\SchoolClassForm;
use App\Filament\Teacher\Resources\SchoolClasses\Schemas\SchoolClassInfolist;
use App\Filament\Teacher\Resources\SchoolClasses\Tables\SchoolClassesTable;
use App\Models\SchoolClass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SchoolClassResource extends Resource
{
    protected static ?string $model = SchoolClass::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $recordTitleAttribute = 'Kelas';
    protected static ?string $navigationLabel = "Kelas";
    protected static ?string $modelLabel = 'kelas';
    protected static ?string $pluralModelLabel = 'List Kelas';

    public static function form(Schema $schema): Schema
    {
        return SchoolClassForm::configure($schema);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('teacher_id',Auth::id());
    }


    public static function infolist(Schema $schema): Schema
    {
        return SchoolClassInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchoolClassesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchoolClasses::route('/'),
            'create' => CreateSchoolClass::route('/create'),
            'view' => ViewSchoolClass::route('/{record}'),
            'edit' => EditSchoolClass::route('/{record}/edit'),
        ];
    }
}
