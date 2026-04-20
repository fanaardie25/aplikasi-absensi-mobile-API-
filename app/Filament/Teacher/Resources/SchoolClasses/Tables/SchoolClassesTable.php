<?php

namespace App\Filament\Teacher\Resources\SchoolClasses\Tables;

use App\Models\SchoolClass;

use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchoolClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Kelas')
                    ->searchable(),
                TextColumn::make('academicYear.year')
                    ->label('Tahun Ajaran'),
                TextColumn::make('total_students')
                    ->label('Jumlah Siswa')
                    ->getStateUsing(function (SchoolClass $record) {
                        return $record->students()->count();
                }),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->icon(fn (bool $state): Heroicon => match ($state) {
                        true => Heroicon::CheckCircle,
                        false => Heroicon::XCircle,
                    })
                    ->color(fn (bool $state): string => match ($state) {
                        false => 'danger',
                        true => 'success',
            })
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
