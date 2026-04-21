<?php

namespace App\Filament\Resources\Agendas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AgendasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Agenda')->searchable(),
                TextColumn::make('start_absensi')->label('Jam Mulai Absensi')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('H:i')),
                TextColumn::make('end_absensi')->label('Jam Batas Akhir Absensi')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('H:i')),
                TextColumn::make('teacher.name')->label('Guru Pengawas/Imam')->searchable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ibadah' => 'Ibadah',
                        'kebersihan' => 'Kebersihan',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'ibadah' => 'success',
                        'kebersihan' => 'warning', 
                        default => 'gray',
                    }),
                IconColumn::make('is_active')->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'), 
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Filter Kategori')
                    ->options([
                        'ibadah' => 'Ibadah',
                        'kebersihan' => 'Kebersihan',
                    ]),
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
