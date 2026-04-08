<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Imports\GuruImporter;
use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Response;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('downloadTemplate')
            ->label('Unduh Template Excel')
            ->icon('heroicon-o-document-arrow-down')
            ->color('gray')
            ->action(function () {
                $columns = ['name', 'email', 'nip']; 
                $csvData = implode(';', $columns) . "\n";
                $csvData .= "ContohNama;contoh@gmail.com;1324672344";

                return Response::streamDownload(function () use ($csvData) {
                    echo $csvData;
                }, 'template_guru.csv');
            }),
            ImportAction::make('importteachers')
                ->label('Import Guru')
                ->importer(GuruImporter::class)
                ->color('info')
                ->icon('heroicon-o-user-group')
                ->after(function () {
                    return redirect(request()->header('Referer'));
                }),
        ];
    }
}
