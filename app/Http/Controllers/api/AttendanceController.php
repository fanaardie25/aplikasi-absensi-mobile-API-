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
            new Middleware('role:admin', except: ['index', 'show']),
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
            ->where('schedule_id', $request->schedule_id)
            ->where('class_id', $user->class_id)
            ->first();


        if (!$rolling) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas kamu tidak terdaftar untuk sholat jumat di sekolah hari ini.'
            ], 403);
        }

        // 2. Cek apakah sudah absen menggunakan ID PIVOT
        $alreadyAttended = Attendance::where('student_id', $user->id)
            ->where('schedule_class_id', $rolling->id) // Pake ID dari pivot
            ->exists();

        if ($alreadyAttended) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah melakukan absensi hari ini.'
            ], 422);
        }

        // --- LOGIC JARAK (Tetap Sama) ---
        $schoolLat = -7.390085504631045;
        $schoolLong = 110.51753388175834;
        $radius = 50; 

        $distance = $this->calculateDistance($request->latitude, $request->longtitude, $schoolLat, $schoolLong);

        if ($distance > $radius) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu berada di luar radius sekolah. Jarak: ' . round($distance) . 'm.'
            ], 403);
        }

        // --- LOGIC SIMPAN FOTO ---
        $image = $request->file('photo');
        $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('attendances', $imageName, 'public');

        // --- SIMPAN KE DATABASE ---
        $attendance = Attendance::create([
            'student_id'        => $user->id,
            'schedule_class_id' => $rolling->id, // INI YANG BENER, ambil dari ID tabel pivot
            'status'            => 'hadir',
            'latitude'          => $request->latitude,
            'longtitude'         => $request->longtitude,
            'photo_path'        => Storage::url($path),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil! Selamat beribadah.',
            'data'    => $attendance
        ], 201);
    }

    public function update(Request $request, $id)
    {
        if (! $attendance = Attendance::find($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance not found',
            ], 404);
        }

        $validated = $request->validate([
            'status'      => ['sometimes', 'in:hadir,tidak_hadir,izin,sakit'],
            'latitude'    => ['sometimes', 'string'],
            'longtitude'   => ['sometimes', 'string'],
            'photo'       => ['sometimes', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'is_verified' => ['sometimes', 'boolean'],
        ]);

        if (isset($validated['photo'])) {
            $image = $validated['photo'];
            $imageName = time() . '_' . $attendance->student_id . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('attendances', $imageName, 'public');
            $validated['photo_path'] = Storage::url($path);
            unset($validated['photo']);
        }

        $attendance->fill($validated);
        $saved = $attendance->save();

        if (! $saved) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save attendance',
            ], 500);
        }

        if (! $attendance->wasChanged()) {
            return response()->json([
                'success' => false,
                'message' => 'No changes were made to the attendance record',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully',
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
