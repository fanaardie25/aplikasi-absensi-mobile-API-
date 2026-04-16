<?php

namespace App\Filament\Resources\SchoolClasses\Tables;


use App\Models\AcademicYear;
use App\Models\SchoolClass; 
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

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
                TextColumn::make('teacher.name')
                    ->label('Guru Pembimbing'),
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
                Filter::make('active')
                    ->label('Kelas Aktif')
                    ->query(fn ($query) => $query->where('is_active', true)),
                SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(AcademicYear::pluck('year', 'id')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                ->requiresConfirmation() 
                ->modalHeading('Hapus Kelas Ini?')
                ->modalDescription('PERINGATAN KERAS: Menghapus kelas ini akan ikut MENGHAPUS SELURUH riwayat absensi siswa yang ada di kelas ini secara permanen. Data yang sudah dihapus tidak dapat dikembalikan. Yakin ingin melanjutkan?')
                ->modalSubmitActionLabel('Ya, Hapus Semua Data')
                ->color('danger'),
            ]) 
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('assignTeacher')
                    ->label('Tetapkan Guru Pembimbing')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->schema([
                        Select::make('teacher_id')
                            ->label('Pilih Guru Pembimbing')
                            ->options(User::where('is_active', true)->where('role', 'teacher')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        
                        foreach ($records as $class) {
                            $class->update([
                                'teacher_id' => $data['teacher_id']
                            ]);
                        }
                    })
                    ->deselectRecordsAfterCompletion()
                    ->modalHeading('Tetapkan Guru Pembimbing')
                    ->modalDescription('Guru yang dipilih akan ditugaskan ke semua kelas yang Anda ceklis.')
                    ->successNotificationTitle('Guru berhasil ditugaskan ke kelas terpilih!')
                ]),

                    Action::make('copyClasses')
                        ->label('Salin Kelas')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('warning')
                        ->schema([
                            Select::make('from_academic_year_id')
                                ->label('Salin DARI Tahun Ajaran:')
                                ->options(AcademicYear::pluck('year', 'id')) 
                                ->required(),
                                
                            Select::make('to_academic_year_id')
                                ->label('Salin KE Tahun Ajaran:')
                                ->options(AcademicYear::pluck('year', 'id'))
                                ->different('from_academic_year_id') 
                                ->required(),
                        ])
                        // PERHATIKAN: $record dihapus dari sini karena kita tidak membutuhkannya lagi
                        ->action(function (array $data): void { 
                            // 1. Ambil semua kelas dari tahun ajaran asal
                            $oldClasses = SchoolClass::where('academic_year_id', $data['from_academic_year_id'])->get();

                            foreach ($oldClasses as $oldClass) {
                                // 2. Duplikat data kelas ke tahun ajaran tujuan
                                SchoolClass::create([
                                    'name' => $oldClass->name,
                                    'academic_year_id' => $data['to_academic_year_id'], 
                                    'teacher_id' => $oldClass->teacher_id,
                                    'is_active' => $oldClass->is_active,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                    'grade' => $oldClass->grade,
                                    'major' => $oldClass->major,
                                    'sequence' => $oldClass->sequence,
                                ]);
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Salin Seluruh Formasi Kelas')
                        ->modalDescription('Proses ini akan menduplikat semua kelas beserta guru pembimbingnya ke tahun ajaran tujuan.')
                        ->successNotificationTitle('Kelas berhasil disalin!'),
            ]);
    }
}
