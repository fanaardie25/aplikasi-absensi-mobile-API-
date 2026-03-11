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
    $user = $request->user()->load(['schoolClass'])
        ->loadCount(['attendances as hadir_count' => function ($query) {
            $query->where('status', 'hadir');
        }]);
    
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
        

        'school_class'       => [
            'id'       => $user->schoolClass->id,
            'name'     => $user->schoolClass->name,
            'grade'    => $user->schoolClass->grade,
            'major'    => $user->schoolClass->major,
            'sequence' => $user->schoolClass->sequence,
        ],

        'is_schedule_open'   => $hasScheduleToday,
        'is_absent_today'    => !is_null($attendanceToday),
        'stats' => [
            'hadir'       => $user->hadir_count,
            'total_pekan' => 15
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
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::get('/activity/latest',[UserController::class,'getLatestActivity']);
    Route::get('/activity/all',[UserController::class,'getAllActivity']);
});

//class 
Route::group(['prefix' => 'class'], function () {
    Route::get('/', [SchoolClassController::class, 'index']);
    Route::post('/', [SchoolClassController::class, 'store']);
    Route::get('/{id}', [SchoolClassController::class, 'show']);
    Route::put('/{id}', [SchoolClassController::class, 'update']);
    Route::delete('/{id}', [SchoolClassController::class, 'destroy']);
});

//schedule
Route::group(['prefix' => 'schedule'], function () {
    Route::get('/', [FridayScheduleController::class, 'index']);
    Route::post('/', [FridayScheduleController::class, 'store']);
    Route::get('/{id}', [FridayScheduleController::class, 'show']);
    Route::put('/{id}', [FridayScheduleController::class, 'update']);
    Route::delete('/{id}', [FridayScheduleController::class, 'destroy']);
});

//attendance
Route::group(['prefix' => 'attendance'], function () {
    Route::get('/', [AttendanceController::class, 'index']);
    Route::post('/', [AttendanceController::class, 'store']);
    Route::get('/{id}', [AttendanceController::class, 'show']);
    Route::put('/{id}', [AttendanceController::class, 'update']);
});