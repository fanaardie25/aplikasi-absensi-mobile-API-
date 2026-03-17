<?php

namespace App\Filament\Widgets;

use App\Models\SchoolClass;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {

        $totalGuru = User::where('role', 'teacher')->where('is_active', true)->count();


        $totalSiswa = User::where('role', 'student')
            ->where('is_active', true)
            ->count();


        $totalKelas = SchoolClass::count();

        return [
            Stat::make('Total Guru', $totalGuru)
                ->description('Guru Aktif')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Total Siswa', $totalSiswa)
                ->description('Siswa aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Total Kelas', $totalKelas)
                ->description('kelas')
                ->descriptionIcon('heroicon-m-rectangle-group')
                ->color('success'),
        ];
    }
}
