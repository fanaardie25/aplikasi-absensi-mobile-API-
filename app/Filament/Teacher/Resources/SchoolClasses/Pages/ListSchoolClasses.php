<?php

namespace App\Filament\Teacher\Resources\SchoolClasses\Pages;

use App\Filament\Teacher\Resources\SchoolClasses\SchoolClassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSchoolClasses extends ListRecords
{
    protected static string $resource = SchoolClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
