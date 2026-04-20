<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Imports\GuruImporter;
use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\HtmlString;

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
                $columns = ['name', 'email', 'nip','gender','religion']; 
                $csvData = implode(';', $columns) . "\n";
                $csvData .= "ContohNama;contoh@gmail.com;1324672344;L;Islam";

                return Response::streamDownload(function () use ($csvData) {
                    echo $csvData;
                }, 'template_guru.csv');
            }),
            ImportAction::make('importteachers')
                ->label('Import Guru')
                ->importer(GuruImporter::class)
                ->color('info')
                ->icon('heroicon-o-user-group')
                ->modalDescription(new HtmlString('
                    Upload file CSV/Excel data guru sesuai template. <br><br>
                    
                    <span style="color: #E74C3C; font-weight: bold;">⚠️ PERHATIAN:</span><br> 
                    Password default untuk semua akun guru yang diimpor adalah <b>guru123!</b><br><br>
                    
                    <span style="color: #F39C12; font-weight: bold;">💡 INFO PENTING:</span><br> 
                    Jika kolom email di Excel dibiarkan kosong, sistem akan otomatis <b>meng-generate email acak</b> untuk guru tersebut.
                '))
                ->after(function () {
                    return redirect(request()->header('Referer'));
                }),
        ];
    }
}
