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
        

        $classes = $schedule->classes; 

        foreach ($classes as $class) {
            $topicName = "class_" . $class->id; 

            $formattedDate = \Carbon\Carbon::parse($schedule->date)->format('d/m/Y');

            $this->sendNotificationToTopic(
                $topicName, 
                "Jadwal Kegiatan Baru 📅", 
                "Jadwal kegiatan untuk tanggal {$formattedDate} sudah tersedia. Pastikan kamu hadir dan mengisi presensi tepat waktu ya!"
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
            Log::error("FCM Detail: " . json_encode($res));
        }
        return $res;
            
        } catch (\Exception $e) {
            Log::error("FCM Error: " . $e->getMessage());
            return false;
        }
    }
}
