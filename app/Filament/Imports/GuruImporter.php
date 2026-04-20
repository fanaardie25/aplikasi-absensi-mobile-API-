<?php

namespace App\Filament\Imports;

use App\Models\Guru;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

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
                ->rules(['nullable', 'email', 'max:255']),

            ImportColumn::make('nip')
                ->label('NIP')
                ->requiredMapping()
                ->rules(['required']),

            ImportColumn::make('gender')
                ->label('Jenis Kelamin')
                ->requiredMapping()
                ->rules(['required', 'in:L,P'])
                ->helperText('L untuk Laki-laki, P untuk Perempuan'),

            ImportColumn::make('religion')
                ->label('Agama')
                ->requiredMapping()
                ->rules(['required', 'in:Islam,Kristen,Katolik,Hindu,Buddha'])
                ->helperText('Pilih salah satu: Islam, Kristen, Katolik, Hindu, Buddha'),
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

        if (blank($this->record->email)) {
            $this->record->email = Str::slug($this->data['name']) . '-' . rand(100, 999) . '@satamail.my.id';
        }

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
