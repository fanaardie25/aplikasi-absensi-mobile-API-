<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\FridaySchedule;
use Illuminate\Console\Command;

class CreateDailyAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate otomatis baris absensi siswa (alpa) berdasarkan jadwal hari ini';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now();
        $dayName = $today->translatedFormat('l');
        $currentTime = $today->format('H:i:s');

        // Load relasi 'agenda' dan 'classes.students' sekaligus untuk mencegah N+1 Query Problem
        $activeSchedules = FridaySchedule::with(['agenda', 'classes.students'])
            ->whereDate('date', $today->toDateString())
            ->get();

        if ($activeSchedules->isEmpty()) {
            $this->info("Tidak ada jadwal untuk hari ($dayName).");
            return;
        }

        $newAttendances = []; 
        $createdAt = $today->toDateTimeString();

        foreach ($activeSchedules as $schedule) {
            
            if ($schedule->agenda && $currentTime < $schedule->agenda->end_absensi) {
                $this->info("Agenda {$schedule->agenda->nama_agenda} belum ditutup (Batas: {$schedule->agenda->end_absensi}). Di-skip dulu.");
                continue;
            }

            foreach ($schedule->classes as $class) {
                $scheduleClassId = $class->pivot->id;
                
                $studentIds = $class->students->pluck('id')->toArray();

                if (empty($studentIds)) {
                    continue;
                }

                $attendedStudentIds = Attendance::where('schedule_class_id', $scheduleClassId)
                    ->whereIn('student_id', $studentIds)
                    ->whereDate('created_at', $today->toDateString())
                    ->pluck('student_id')
                    ->toArray();

        
                $alpaStudentIds = array_diff($studentIds, $attendedStudentIds);
                foreach ($alpaStudentIds as $studentId) {
                    $newAttendances[] = [
                        'student_id'        => $studentId,
                        'schedule_class_id' => $scheduleClassId,
                        'photo_path'        => 'null',
                        'status'            => 'tidak_hadir',
                        'longtitude'        => '0',
                        'latitude'          => '0',
                        'created_at'        => $createdAt,
                        'updated_at'        => $createdAt, 
                        'class_id'          => $class->id,
                    ];
                }
            }
        }
        if (!empty($newAttendances)) {
            Attendance::insert($newAttendances);
            $this->info("Berhasil membuat " . count($newAttendances) . " absensi otomatis (alpa).");
        } else {
            $this->info("Aman! Semua siswa sudah absen atau belum waktunya tutup absen.");
        }
    }
}