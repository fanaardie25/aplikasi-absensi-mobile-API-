<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use Filament\Resources\Pages\CreateRecord;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function afterCreate(): void
    {
        $schedule = $this->record;
        
        // 1. Ambil target dari agenda & bersihkan formatnya (lowercase & tanpa spasi)
        // Kita pakai default 'all' kalau misalnya datanya kosong
        $targetGender = strtolower(str_replace(' ', '_', $schedule->agenda->target_gender ?? 'all'));
        $targetReligion = strtolower(str_replace(' ', '_', $schedule->agenda->target_religion ?? 'all'));
        $agendaName = $schedule->agenda->name ?? 'Kegiatan';

        $classes = $schedule->classes; 

        foreach ($classes as $class) {
            $classId = strtolower(str_replace(' ', '_', $class->id));

            // PENENTUAN TOPIC TEPAT SASARAN
            if ($targetGender === 'all' && $targetReligion === 'all') {
                // Level 1: Semua anak
                $topicName = "class_" . $classId; 
                
            } elseif ($targetGender !== 'all' && $targetReligion === 'all') {
                // Level 2: Spesifik Gender saja
                $topicName = "class_" . $classId . "_" . $targetGender;
                
            } elseif ($targetGender === 'all' && $targetReligion !== 'all') {
                // Level 3: Spesifik Agama saja 
                $topicName = "class_" . $classId . "_" . $targetReligion;
                
            } else {
                // Level 4: Spesifik Gender & Agama
                $topicName = "class_" . $classId . "_" . $targetGender . "_" . $targetReligion;
            }

            $formattedDate = \Carbon\Carbon::parse($schedule->date)->format('d/m/Y');
            $this->sendNotificationToTopic(
                $topicName, 
                "Jadwal $agendaName Baru 📅", 
                "Jadwal untuk tanggal {$formattedDate} sudah tersedia. Pastikan kamu hadir dan mengisi presensi ya!"
            );
        }
    }

    /**
     * Fungsi untuk mendapatkan Access Token dari Firebase
     */
    private function getGoogleAccessToken()
    {
        $file = storage_path('app/firebase_credential.json'); 
        
        if (!file_exists($file)) {
            throw new \Exception("File firebase_credential.json tidak ditemukan di storage/app/");
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
                    ],
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'channel_id' => 'absensi_channel_v2', 
                            'sound' => 'default'
                        ],
                    ],
                ],
            ]);

            $res = $response->json();
            
            if ($response->successful()) {
                Log::info("FCM Sukses kirim ke topic [$topic]: " . json_encode($res));
            } else {
                Log::error("FCM Gagal: " . json_encode($res));
            }
            
            return $res;
            
        } catch (\Exception $e) {
            Log::error("FCM Error: " . $e->getMessage());
            return false;
        }
    }
}