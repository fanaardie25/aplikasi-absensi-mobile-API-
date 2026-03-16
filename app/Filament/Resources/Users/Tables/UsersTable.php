<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\SchoolClass;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable()
            ->persistSearchInSession()
            ->columns([
                ImageColumn::make('profile_photo_path')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => "https://ui-avatars.com/api/?name=" . urlencode($record->name) . "&background=10B981&color=fff")
                    ->label('Photo Profile')
                    ->imageSize(40)
                    ->extraImgAttributes(['loading' => 'lazy']),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(), 
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable(),
                TextColumn::make('schoolClass.name')
                    ->label('Kelas')
                    ->searchable(),
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
                Filter::make('is_floating')
                ->label('Siswa Belum Ada Kelas')
                ->query(fn (EloquentBuilder $query) => $query->whereNull('class_id'))
                ->toggle(),
    
                SelectFilter::make('class_id')
                    ->relationship('schoolClass', 'name')
                    ->label('Filter per Kelas'),
                TernaryFilter::make('is_active')
                ->label('Status Siswa')
                ->placeholder('Semua Status')
                ->trueLabel('Hanya Aktif')
                ->falseLabel('Sudah Lulus/Non-Aktif')
                ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('graduateStudents')
                    ->label('Luluskan Siswa Terpilih')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->requiresConfirmation() 
                    ->modalHeading('Konfirmasi Kelulusan')
                    ->modalDescription('Apakah kamu yakin ingin meluluskan siswa yang dipilih? Mereka akan dinonaktifkan dan dilepas dari kelas.')
                    ->modalSubmitActionLabel('Ya, Luluskan')
                    ->action(function (Collection $records) {
                        $records->each(function ($student) {
                            $student->update([
                                'is_active' => false,
                                'class_id' => null,
                            ]);
                        });
                    })
                    ->deselectRecordsAfterCompletion(), 

                    BulkAction::make('setFloating')
                        ->label('Set Jadi Floating (Naik Kelas)')
                        ->icon('heroicon-o-arrows-right-left')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $records->each->update(['class_id' => null]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('assignToClass')
                        ->label('Pindahkan ke Kelas')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->color('info')
                        ->schema([
                            Select::make('new_class_id')
                            ->label('Pilih Kelas Tujuan')
                            ->options(
                                SchoolClass::whereHas('academicYear', function ($query) {
                                    $query->where('is_active', true);
                                })->pluck('name', 'id')
                            )
                            ->required()
                            ->helperText('Hanya menampilkan kelas di Tahun Ajaran yang sedang aktif.')
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function ($student) use ($data) {
                                $student->update([
                                    'class_id' => $data['new_class_id'],
                                    'is_active' => true, 
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('Pindahkan Siswa ke Kelas Baru')
                        ->modalDescription('Semua siswa yang dipilih akan dimasukkan ke kelas yang Anda tentukan.')
                        ->modalSubmitActionLabel('Pindahkan Sekarang'),
                ]),
            ]);
    }
}
