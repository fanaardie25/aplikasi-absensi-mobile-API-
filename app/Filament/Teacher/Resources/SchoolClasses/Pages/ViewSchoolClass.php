<?php

namespace App\Filament\Teacher\Resources\SchoolClasses\Pages;

use App\Filament\Teacher\Resources\SchoolClasses\SchoolClassResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSchoolClass extends ViewRecord
{
    protected static string $resource = SchoolClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
