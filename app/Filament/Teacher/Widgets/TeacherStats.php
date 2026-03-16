<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Attendance;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TeacherStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $teacherId = Auth::id();

        $totalSiswa = User::where('role', 'student')
            ->where('is_active',true)
            ->whereHas('schoolClass', fn($q) => $q->where('teacher_id', $teacherId))
            ->count();

        $hadirHariIni = Attendance::whereDate('created_at', today())
            ->where('status', 'hadir')
            ->whereHas('class', fn($q) => $q->where('teacher_id', $teacherId))
            ->count();
        $belumAbsen = $totalSiswa - $hadirHariIni;

        return [
            Stat::make('Total Siswa Bimbingan', $totalSiswa)
                ->description('Total siswa aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Hadir Hari Ini', $hadirHariIni)
                ->description('Siswa yang sudah absen')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([7, 10, 5, 12, $hadirHariIni])
                ->color('success'),

            Stat::make('Belum Absen', $belumAbsen)
                ->description('Siswa aktif yang belum presensi')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($belumAbsen > 0 ? 'danger' : 'success'),
        ];
    }
}
