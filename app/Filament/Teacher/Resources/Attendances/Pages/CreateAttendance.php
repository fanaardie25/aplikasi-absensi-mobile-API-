<?php

namespace App\Filament\Teacher\Resources\Attendances\Pages;

use App\Filament\Teacher\Resources\Attendances\AttendanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;
}
