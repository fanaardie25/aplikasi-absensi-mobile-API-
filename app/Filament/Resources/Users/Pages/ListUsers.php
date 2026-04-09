<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Imports\SiswaImporter;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Response;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('downloadTemplate')
            ->label('Unduh Template Excel')
            ->icon('heroicon-o-document-arrow-down')
            ->color('gray')
            ->action(function () {
                $columns = ['name', 'email', 'nis','gender','religion']; 
                $csvData = implode(';', $columns) . "\n";
                $csvData .= "Contoh Nama;contoh@satamail.my.id;p.12345;L;Islam";

                return Response::streamDownload(function () use ($csvData) {
                    echo $csvData;
                }, 'template_siswa.csv');
            }),
            ImportAction::make('importStudents')
                ->label('Import Siswa')
                ->importer(SiswaImporter::class)
                ->color('info')
                ->icon('heroicon-o-user-group')
                ->after(function () {
                    return redirect(request()->header('Referer'));
                }),
        ];
    }
}
