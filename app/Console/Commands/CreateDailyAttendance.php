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
    protected $description = 'Generate otomatis baris absensi siswa berdasarkan jadwal Jumat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now();
        $dayName = $today->translatedFormat('l');

        $activeSchedules = FridaySchedule::with('classes')
            ->whereDate('date', $today->toDateString())
            ->get();

        if ($activeSchedules->isEmpty()) {
            $this->info("Tidak ada jadwal untuk hari ($dayName).");
            return;
        }

        foreach ($activeSchedules as $schedule) {
            foreach ($schedule->classes as $class) {
                $scheduleClassId = $class->pivot->id;

                $students = $class->students;

                foreach ($students as $student) {
                    $alreadyExists = Attendance::where('student_id', $student->id)
                        ->where('schedule_class_id', $scheduleClassId)
                        ->whereDate('created_at', $today->toDateString())
                        ->exists();

                    if (!$alreadyExists) {
                        Attendance::create([
                            'student_id'        => $student->id,
                            'schedule_class_id' => $scheduleClassId,
                            'photo_path'        => "null",
                            'status'            => 'tidak_hadir',
                            'longtitude'        => 0,
                            'latitude'          => 0,
                            'created_at'        => $today,
                        ]);
                    }
                }
            }
        }

        $this->info("Berhasil membuat absensi otomatis untuk jadwal hari ini.");
    }
}
