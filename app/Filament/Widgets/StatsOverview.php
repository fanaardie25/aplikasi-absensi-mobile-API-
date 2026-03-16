<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        
    $today = now()->toDateString();

    $activeStudentQuery = User::where('role', 'student')->where('is_active', true);
    
    $totalSiswaAktif = (clone $activeStudentQuery)->count();
    $sudahAbsen = Attendance::whereDate('created_at', $today)->count();

    return [
        Stat::make('Total Siswa Aktif', $totalSiswaAktif)
            ->description('Jumlah siswa yang aktif sekolah')
            ->descriptionIcon('heroicon-m-users')
            ->color('info'),

        Stat::make('Hadir Hari Ini', $sudahAbsen)
            ->description('Siswa yang sudah absen')
            ->descriptionIcon('heroicon-m-check-badge')
            ->chart([7, 2, 10, 3, 15, 4, 17]) 
            ->color('success'),

        Stat::make('Belum Absen', $totalSiswaAktif - $sudahAbsen)
            ->description('Siswa aktif yang belum presensi')
            ->descriptionIcon('heroicon-m-x-circle')
            ->color($totalSiswaAktif - $sudahAbsen > 0 ? 'danger' : 'success'),
    ];
    }
}
