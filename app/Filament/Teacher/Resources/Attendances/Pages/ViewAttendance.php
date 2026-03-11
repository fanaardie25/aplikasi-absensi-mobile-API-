<?php

namespace App\Filament\Teacher\Resources\Attendances\Pages;

use App\Filament\Teacher\Resources\Attendances\AttendanceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAttendance extends ViewRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
