<?php

namespace App\Filament\Teacher\Resources\Attendances\Pages;

use App\Filament\Teacher\Resources\Attendances\AttendanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
