<?php

namespace App\Filament\Resources\SchoolClasses\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
                TextColumn::make('class_teacher')
                    ->label('Wali Kelas'),
                TextColumn::make('academic_year')
                    ->label('Tahun Ajaran'),
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
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
