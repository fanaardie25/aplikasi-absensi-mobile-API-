<?php

namespace App\Filament\Resources\SchoolClasses\Schemas;

use App\Models\AcademicYear;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class SchoolClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([
            Section::make('Data Kelas')
            ->description('Nama kelas akan terbentuk otomatis berdasarkan inputan Anda.')
            ->schema([
                TextInput::make('name')
                    ->label('Nama Kelas')
                    ->readonly()
                    ->required()
                    ->placeholder('Otomatis: 10 RPL 1')
                    ->columnSpanFull() 
                    ->dehydrated(),

                Grid::make(3)
                    ->schema([
                        Select::make('grade')
                            ->label('Kelas')
                            ->options([
                                '10' => 'Kelas 10',
                                '11' => 'Kelas 11',
                                '12' => 'Kelas 12',
                            ])
                            ->required()
                            ->native(false)
                            ->live() 
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::updateName($set, $get)),

                        TextInput::make('major')
                            ->label('Jurusan')
                            ->placeholder('Contoh: RPL')
                            ->required()
                            ->live(onBlur: true) 
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::updateName($set, $get)),

                        TextInput::make('sequence')
                            ->label('Urutan')
                            ->placeholder('Contoh: 1')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::updateName($set, $get)),
                    ]),

                Grid::make(2)
                    ->schema([
                        Select::make('teacher_id')
                            ->label('Guru Pembimbing')
                            ->searchable()
                            ->relationship(
                                'teacher', 
                                'name',
                                modifyQueryUsing: fn (Builder $query) => $query->where('role', 'teacher')->where('is_active',true)
                            )
                            ->preload(),

                        Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->options(
                                \App\Models\AcademicYear::all()->mapWithKeys(function ($item) {
                                    return [$item->id => $item->year . ($item->is_active ? ' (Aktif)' : '')];
                                })
                            )
                            ->default(\App\Models\AcademicYear::where('is_active', true)->first()?->id) 
                            ->required(),
                    ]),

                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(true),
            ]),
         ])->columns(1);
    }


    public static function updateName(Set $set, Get $get): void
    {
        $grade = $get('grade');
        $major = $get('major');
        $sequence = $get('sequence');

        $name = collect([$grade, $major, $sequence])
            ->filter()
            ->implode(' ');

        $set('name', strtoupper($name));
    }

}
