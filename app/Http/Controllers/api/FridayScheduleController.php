<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\FridaySchedule;
use Carbon\Carbon;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Storage;


class FridayScheduleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth:sanctum',
            new Middleware('role:admin', except: ['index', 'show']),
        ];
    }

    
    /**
     * Fungsi untuk mendapatkan Access Token dari Firebase
     */
    private function getGoogleAccessToken()
    {
        $file = storage_path('app/firebase_credential.json'); 
        
        if (!file_exists($file)) {
            throw new \Exception("File firebase_credentials.json tidak ditemukan di storage/app/");
        }

        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $file);
        $token = $credentials->fetchAuthToken(HttpHandlerFactory::build());
        
        return $token['access_token'];
    }

    /**
     * Fungsi Utama Kirim Notif ke Topic
     */
    public function sendNotificationToTopic($topic, $title, $body)
    {
        try {
            $accessToken = $this->getGoogleAccessToken();
            
            $fileContent = json_decode(file_get_contents(storage_path('app/firebase_credential.json')), true);
            $projectId = $fileContent['project_id']; 

            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $response = Http::withToken($accessToken)->post($url, [
            'message' => [
                'topic' => $topic,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => [
                    'type' => 'new_schedule',
                    // Tambahkan data lain jika perlu
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'channel_id' => 'absensi_channel', 
                        'sound' => 'default'
                    ],
                ],
            ],
        ]);

        $res = $response->json();
        if ($response->successful()) {
            FacadesLog::error("FCM Detail: " . json_encode($res));
        }
        return $res;
            
        } catch (\Exception $e) {
            FacadesLog::error("FCM Error: " . $e->getMessage());
            return false;
        }
    }

    public function index()
    {
        $schedules = FridaySchedule::with('classes')->orderBy('date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'date'        => ['required', 'date', 'unique:friday_schedules,date'],
            'description' => ['nullable', 'string'],
            'class_ids'   => ['required', 'array'],
            'class_ids.*' => ['exists:school_classes,id'],
        ]);

        $input_date = Carbon::parse($request->date);
        $currentDay = Carbon::today();

       // 1. Pastikan hari yang dipilih adalah hari Jumat
        if ($input_date->dayOfWeek !== Carbon::FRIDAY) {
            return response()->json([
                'success' => false,
                'message' => 'Pembuatan jadwal gagal. Tanggal yang dipilih harus jatuh pada hari Jumat.'
            ], 422);
        }

        // 2. Pastikan tidak memilih hari Jumat yang sudah berlalu
        if ($input_date->isPast() && !$input_date->isToday()) {
            return response()->json([
                'success' => false,
                'message' => 'Pembuatan jadwal gagal. Tidak dapat membuat jadwal untuk tanggal yang sudah terlampaui.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $schedule = FridaySchedule::create([
                'date'        => $request->date,
                'description' => $request->description,
            ]);

            $schedule->classes()->attach($request->class_ids);

            DB::commit();
            
            foreach ($request->class_ids as $id) {
                $topicName = "class_" . $id; 

                $this->sendNotificationToTopic(
                    $topicName, 
                    "Jadwal Jumat Baru! 📅", 
                    "Kamu mendapatkan giliran. Yuk cek aplikasimu!"
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Friday schedule created successfully',
                'data'    => $schedule->load('classes')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $schedule = FridaySchedule::with('classes')->find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found'
                ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $schedule
        ]);
    }

    public function destroy($id)
    {
        $schedule = FridaySchedule::find($id);
        
        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found'
                ], 404);
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully'
        ]);
    }

    public function update(Request $request, $id)
    {
        if (! $schedule = FridaySchedule::find($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found',
            ], 404);
        }

        $validated = $request->validate([
            'date'        => ['sometimes', 'date', 'unique:friday_schedules,date,' . $id],
            'description' => ['nullable', 'string'],
            'class_ids'   => ['sometimes', 'array'],
            'class_ids.*' => ['exists:school_classes,id'],
        ]);


        $classIds = $validated['class_ids'] ?? null;
        unset($validated['class_ids']);

        try {
            DB::transaction(function () use ($schedule, $validated, $classIds) {
                $schedule->update($validated);

                if ($classIds) {
                    $schedule->classes()->sync($classIds);
                }
            });


            return response()->json([
                'success' => true,
                'message' => 'Friday schedule updated successfully',
                'data'    => $schedule->load('classes'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update schedule: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}
