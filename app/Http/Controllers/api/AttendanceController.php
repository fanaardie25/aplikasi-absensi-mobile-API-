<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ScheduleClass;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

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
        // 1. Ambil setting lokasi & radius saja
        $settings = Setting::whereIn('key', [
            'school_latitude', 
            'school_longitude', 
            'attendance_radius'
        ])->pluck('value', 'key');

        // 2. Validasi input (kembali pakai schedule_id yang merujuk ke master jadwal)
        $request->validate([
            'schedule_id' => ['required', 'exists:friday_schedules,id'],
            'latitude'    => ['required', 'string'],
            'longtitude'  => ['required', 'string'],
            'photo'       => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], 
        ]);

        $user = Auth::user();
        $now = now(); 
        
        // 3. Ambil master jadwal yang dipilih
        $schedule = \App\Models\FridaySchedule::find($request->schedule_id);

        // --- LOGIKA PEMBATASAN WAKTU DARI TABEL JADWAL ---
        $start = now()->setTimeFromTimeString($schedule->agenda->start_absensi);
        $end = now()->setTimeFromTimeString($schedule->agenda->end_absensi);

        if ($now->lt($start)) {
            $jamBuka = \Carbon\Carbon::parse($schedule->agenda->start_absensi)->format('H:i');
            return response()->json([
                'success' => false,
                'message' => "Absen belum dibuka. Silakan kembali pada jam {$jamBuka}."
            ], 403);
        }

        if ($now->gt($end)) {
            $jamTutup = \Carbon\Carbon::parse($schedule->agenda->end_absensi)->format('H:i');
            return response()->json([
                'success' => false,
                'message' => "Waktu absen sudah habis (Batas jam {$jamTutup})."
            ], 403);
        }
        // -----------------------------------------------
    
        // 4. LOGIKA PEMBATASAN KELAS
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
                'message' => 'Kelas kamu tidak terdaftar untuk mengikuti agenda ini.'
            ], 403);
        }
  
        // 5. Cek Duplikasi Absen di Tabel ATTENDANCES
        $alreadyAttended = \App\Models\Attendance::where('student_id', $user->id)
            ->where('schedule_class_id', $rolling->id)
            ->whereDate('created_at', $now->toDateString())
            ->exists();

        if ($alreadyAttended) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah absen untuk jadwal ini hari ini.'
            ], 422);
        }
    
        // 6. Validasi Radius Jarak
        $schoolLat = $settings->get('school_latitude', -7.390022513649234);
        $schoolLong = $settings->get('school_longitude', 110.51808635390792);
        $radius = (int) $settings->get('attendance_radius', 100);

        $distance = $this->calculateDistance($request->latitude, $request->longtitude, $schoolLat, $schoolLong);

        if ($distance > $radius) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu berada di luar radius sekolah. Jarak: ' . round($distance) . 'm.'
            ], 403);
        }

        // 7. Proses Foto
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $imageName = time() . '_' . $user->id . '.webp'; 
            
            $image = Image::read($file);
            $image->scale(width: 600); 
            $encoded = $image->toWebp(65);
            
            Storage::disk('public')->put('attendances/' . $imageName, (string) $encoded);
            $path = 'attendances/' . $imageName;
        }

        // 8. Simpan Data Absensi ke Tabel ATTENDANCES
        try {
            $attendance = \App\Models\Attendance::create([
                'student_id'        => $user->id,
                'schedule_class_id' => $rolling->id, 
                'status'            => 'hadir',
                'latitude'          => $request->latitude,
                'longtitude'        => $request->longtitude,
                'photo_path'        => $path,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan absensi!',
                'data'    => $attendance
            ], 200);

        } catch (\Exception $e) {
            if (isset($path)) Storage::disk('public')->delete($path);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
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
