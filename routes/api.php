<?php

use App\Http\Controllers\api\AttendanceController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\FridayScheduleController;
use App\Http\Controllers\api\SchoolClassController;
use App\Http\Controllers\api\UserController;
use App\Models\Attendance;
use App\Models\ScheduleClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

Route::get('/me', function (Request $request) {
    $user = $request->user()->load(['schoolClass.teacher'])
    ->loadCount([
        'attendances as hadir_count' => function ($query) {
            $query->where('status', 'hadir');
        },
        'attendances as alpa_count' => function ($query) {
            $query->where('status', 'tidak_hadir');
        },
        'attendances as izin_count' => function ($query) {
            $query->where('status', 'izin');
        },
        'attendances as sakit_count' => function ($query) {
            $query->where('status', 'sakit');
        },
    ]);
    
    $todayDate = now()->format('Y-m-d');

    // --- BAGIAN YANG DI-UPDATE (FILTER GENDER & AGAMA) ---
    $todaySchedules = ScheduleClass::with('fridaySchedule.agenda')
        ->where('class_id', $user->class_id)
        ->whereHas('fridaySchedule', function ($query) use ($todayDate, $user) {
            $query->whereDate('date', $todayDate)
                  // Kita tambah pengecekan ke tabel agendas
                  ->whereHas('agenda', function ($agendaQuery) use ($user) {
                      $agendaQuery
                          // 1. Ambil yang targetnya 'ALL' atau sesuai gender santri
                          ->whereIn('target_gender', ['ALL', $user->gender])
                          // 2. Ambil yang targetnya 'ALL' atau sesuai agama santri
                          ->whereIn('target_religion', ['ALL', $user->religion]);
                  });
        })
        ->get()
        ->sortBy(function ($scheduleClass) {
            return $scheduleClass->fridaySchedule->agenda->start_absensi ?? '00:00:00';
        });
    // ----------------------------------------------------

    $userSchedule = null;
    $attendanceToday = null;

    foreach ($todaySchedules as $schedule) {
        $absen = Attendance::where('student_id', $user->id)
            ->where('schedule_class_id', $schedule->id) 
            ->whereDate('created_at', $todayDate)
            ->first();

        $userSchedule = $schedule; 
        if (!$absen) {
            $attendanceToday = null;
            break; 
        } else {
            $attendanceToday = $absen;
        }
    }

    $hasScheduleToday = !is_null($userSchedule);

    return response()->json([
        'id'                 => $user->id,  
        'name'               => $user->name,
        'email'              => $user->email,
        'must_change_password' => (bool) $user->must_change_password,
        'role'               => $user->role, 
        'nis'                => $user->nis,  
        'gender'             => $user->gender,
        'religion'           => $user->religion,
        'class_id'           => $user->class_id ?? null,
        'profile_photo_path' => $user->profile_photo_path ?? '',
        'schedule_id'       =>  $userSchedule?->schedule_id ?? null,
        
        'agenda_name'        => $userSchedule?->fridaySchedule?->agenda?->name ?? null,
        'start_absensi' => $userSchedule?->fridaySchedule?->agenda?->start_absensi ?? null,
        'end_absensi'   => $userSchedule?->fridaySchedule?->agenda?->end_absensi ?? null,

        'school_class' => $user->schoolClass ? [
            'id'       => $user->schoolClass->id,
            'name'     => $user->schoolClass->name,
            'grade'    => $user->schoolClass->grade,
            'major'    => $user->schoolClass->major,
            'sequence' => $user->schoolClass->sequence,
        ] : null,

        'teacher' => $user->schoolClass?->teacher?->name ?? null,

        'is_schedule_open'   => $hasScheduleToday,
        'is_absent_today'    => !is_null($attendanceToday),
        'stats' => [
            'hadir'       => $user->hadir_count,
            'tidak_hadir' => $user->alpa_count,
            'izin'        => $user->izin_count,
            'sakit'       => $user->sakit_count,
        ]
    ]);
})->middleware('auth:sanctum');

//auth
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/change-password', function (Request $request) {
        $userId = $request->user()->id;
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'unique:users,email,' . $userId],
            'old_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', 'min:8'],
        ], [
            'email.unique' => 'Email sudah digunakan oleh siswa lain!',
            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Format email tidak valid!',
            'old_password.current_password' => 'Password lama kamu salah!',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'new_password.min' => 'Password minimal 8 karakter!',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() 
            ], 422); 
        }

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->new_password), 
            'email' => $request->email,
            'must_change_password' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email dan Password berhasil diperbarui!'
        ]);
    })->middleware('auth:sanctum');
});

//user
Route::group(['prefix' => 'user'], function () {
    Route::get('/activity/latest',[UserController::class,'getLatestActivity']);
    Route::get('/activity/all',[UserController::class,'getAllActivity']);
    Route::post('/update/profile', [UserController::class, 'updatePhotoProfile']);
});

//attendance
Route::group(['prefix' => 'attendance'], function () {
    Route::post('/', [AttendanceController::class, 'store']);
});