<?php

namespace App\Filament\Resources\Schedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->label('Tanggal Kegiatan'),

                TextColumn::make('agenda.name')
                    ->label('Agenda / Kegiatan')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('classes.name')
                    ->label('Kelas')
                    ->badge()
                    ->searchable()
                    ->description(fn ($record) => $record->classes->first()?->academicYear->year ?? '-'),

                TextColumn::make('agenda.teacher.name')
                    ->label('Imam / Pengawas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), 

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(30) 
                    ->searchable(),
            ])
           
            ->defaultSort('date', 'desc') 
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
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