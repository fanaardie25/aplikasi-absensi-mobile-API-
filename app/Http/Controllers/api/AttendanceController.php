<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ScheduleClass;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:sanctum',
            new Middleware('role:admin', except: ['index', 'show','store']),
        ];
    }

    public function index()
    {
        $attendance = Attendance::with('student')->get();

        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);
    }

    public function show($id) 
    {
        $attendance = Attendance::with('student')->find($id);

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'attendance is not found'
            ],404);
        }

        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);

    }
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => ['required', 'exists:friday_schedules,id'],
            'latitude'    => ['required', 'string'],
            'longtitude'   => ['required', 'string'],
            'photo'       => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], 
        ]);

        $user = Auth::user();
    
        $rolling = DB::table('schedule_classes')
            ->join('school_classes', 'schedule_classes.class_id', '=', 'school_classes.id') 
            ->where('schedule_id', $request->schedule_id)
            ->where('class_id', $user->class_id)
            ->where('school_classes.is_active', 1)
            ->select('schedule_classes.*')
            ->first();


        if (!$rolling) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak terdaftar atau sudah tidak aktif.'
            ], 403);
        }

  
        $alreadyAttended = Attendance::where('student_id', $user->id)
            ->where('schedule_class_id', $rolling->id)
            ->exists();

        if ($alreadyAttended) {
            return response()->json([
                'success' => false,
                'message' => 'You have already checked in today.'
            ], 422);
        }

    
        $schoolLat = -7.390085504631045;
        $schoolLong = 110.51753388175834;
        $radius = 50; 

        $distance = $this->calculateDistance($request->latitude, $request->longtitude, $schoolLat, $schoolLong);

        if ($distance > $radius) {
            return response()->json([
                'success' => false,
                'message' => 'You are outside the school radius. Distance: ' . round($distance) . 'm.'
            ], 403);
        }

     
        $image = $request->file('photo');
        $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('attendances', $imageName, 'public');

       
        $attendance = Attendance::create([
            'student_id'        => $user->id,
            'schedule_class_id' => $rolling->id, 
            'status'            => 'hadir',
            'latitude'          => $request->latitude,
            'longtitude'         => $request->longtitude,
            'photo_path'        => Storage::url($path),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully!',
            'data'    => $attendance
        ], 200);
    }

    public function update(Request $request, $id)
    {
        if (! $attendance = Attendance::find($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance data not found',
            ], 404);
        }

        $validated = $request->validate([
            'status'      => ['sometimes', 'in:hadir,tidak_hadir,izin,sakit'],
            'is_verified' => ['sometimes', 'boolean'],
        ]);

        $attendance->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Attendance status updated successfully',
            'data'    => $attendance,
        ]);
    }


    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
