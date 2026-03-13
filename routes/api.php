<?php

use App\Http\Controllers\api\AttendanceController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\FridayScheduleController;
use App\Http\Controllers\api\SchoolClassController;
use App\Http\Controllers\api\UserController;
use App\Models\Attendance;
use App\Models\ScheduleClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/me', function (Request $request) {
    $user = $request->user()->load(['schoolClass.teacher'])
    ->loadCount([
        'attendances as hadir_count' => function ($query) {
            $query->where('status', 'hadir');
        },
        'attendances as alpa_count' => function ($query) {
            $query->where('status', 'tidak_hadir');
        },
    ]);
    
    $todayDate = now()->format('Y-m-d');

    $userSchedule = ScheduleClass::where('class_id', $user->class_id)
        ->whereHas('fridaySchedule', function ($query) use ($todayDate) {
            $query->whereDate('date', $todayDate);
        })
        ->first();

    $hasScheduleToday = !is_null($userSchedule);

    $attendanceToday = Attendance::where('student_id', $user->id)
        ->whereDate('created_at', $todayDate)
        ->first();


    return response()->json([
        'id'                 => $user->id,  
        'name'               => $user->name,
        'email'              => $user->email,
        'role'               => $user->role, 
        'nis'                => $user->nis,  
        'class_id'           => $user->class_id ?? null,
        'profile_photo_path' => $user->profile_photo_path ?? '',
        'schedule_id'       =>  $userSchedule->schedule_id ?? null,
        

        'school_class' => $user->schoolClass ? [
            'id'       => $user->schoolClass->id,
            'name'     => $user->schoolClass->name,
            'grade'    => $user->schoolClass->grade,
            'major'    => $user->schoolClass->major,
            'sequence' => $user->schoolClass->sequence,
        ] : null,

        'teacher' => $user->schoolClass->teacher->name ?? null,

        'is_schedule_open'   => $hasScheduleToday,
        'is_absent_today'    => !is_null($attendanceToday),
        'stats' => [
            'hadir'       => $user->hadir_count,
            'tidak_hadir' => $user->alpa_count
        ]
    ]);
})->middleware('auth:sanctum');

//auth
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
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