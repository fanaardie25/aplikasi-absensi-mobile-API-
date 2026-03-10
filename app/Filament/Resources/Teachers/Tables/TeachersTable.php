<?php

namespace App\Filament\Resources\Teachers\Tables;

use App\Models\SchoolClass;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label('Nama Guru')
                ->searchable(),
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('assignTeacherToClass')
                    ->label('Assign ke Kelas')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Select::make('class_id')
                            ->label('Pilih Kelas')
                            ->options(SchoolClass::whereNull('teacher_id')->pluck('name', 'id'))
                            ->multiple()
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) { 
                        $records->each(function ($teacher) use ($data) {
                            SchoolClass::whereIn('id', $data['class_id'])
                                ->update(['teacher_id' => $teacher->id]);
                        });
                    })
                ]),
            ]);
    }
}
