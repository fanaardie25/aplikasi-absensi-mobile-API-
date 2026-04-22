<?php

namespace App\Filament\Resources\Holidays\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class HolidaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Libur')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Tanggal Libur')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(50),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
