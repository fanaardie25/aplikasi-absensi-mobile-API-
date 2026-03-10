<?php

namespace App\Filament\Imports;

use App\Models\Guru;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Number;

class GuruImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nama Lengkap')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('email')
                ->label('Email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),

            ImportColumn::make('nip')
                ->label('NIP')
                ->requiredMapping()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): User
    {
       return User::firstOrNew([
            'nip' => $this->data['nip'],
        ]);
    }

    protected function beforeSave(): void
    {
        $this->record->role = 'teacher';
        $this->record->is_active = true;

        if (! $this->record->exists) {
            $this->record->password = Hash::make('guru123!');
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import guru selesai. ' . Number::format($import->successful_rows) . ' data berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' data gagal (cek file log).';
        }

        return $body;
    }
}
