<?php

namespace App\Filament\Teacher\Resources\Attendances;

use App\Filament\Teacher\Resources\Attendances\Pages\CreateAttendance;
use App\Filament\Teacher\Resources\Attendances\Pages\EditAttendance;
use App\Filament\Teacher\Resources\Attendances\Pages\ListAttendances;
use App\Filament\Teacher\Resources\Attendances\Pages\ViewAttendance;
use App\Filament\Teacher\Resources\Attendances\Schemas\AttendanceForm;
use App\Filament\Teacher\Resources\Attendances\Schemas\AttendanceInfolist;
use App\Filament\Teacher\Resources\Attendances\Tables\AttendancesTable;
use App\Models\Attendance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CheckCircle;

    protected static ?string $recordTitleAttribute = 'Kehadiran';
        protected static ?string $navigationLabel = "kehadiran";
    protected static ?string $modelLabel = 'Kehadiran';
    protected static ?string $pluralModelLabel = 'List Kehadiran';
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
        ->with('class','student','scheduleClass.fridaySchedule.agenda')
        ->whereHas('class', function ($query) {
            $query->where('teacher_id', Auth::id());
        });
    }

    public static function form(Schema $schema): Schema
    {
        return AttendanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AttendanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table);
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
            'index' => ListAttendances::route('/'),
            'create' => CreateAttendance::route('/create'),
            'view' => ViewAttendance::route('/{record}'),
            'edit' => EditAttendance::route('/{record}/edit'),
        ];
    }
}
