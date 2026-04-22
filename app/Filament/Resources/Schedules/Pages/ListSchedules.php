<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use App\Models\Agenda;
use App\Models\FridaySchedule;
use App\Models\SchoolClass;
use App\Models\Holiday;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateMonthlyAuto')
                ->label('Generate Bulanan Auto')
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                ->form([
                    Select::make('agenda_id')
                        ->label('Agenda')
                        ->options(fn () => Agenda::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),
                    CheckboxList::make('selected_days')
                        ->label('Hari')
                        ->options([
                            1 => 'Senin',
                            2 => 'Selasa',
                            3 => 'Rabu',
                            4 => 'Kamis',
                            5 => 'Jumat',
                            6 => 'Sabtu',
                            7 => 'Minggu',
                        ])
                        ->required()
                        ->columns(4),
                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ])
                        ->default(now()->month)
                        ->required(),
                    Select::make('year')
                        ->label('Tahun')
                        ->options(function () {
                            $currentYear = date('Y');
                            return [
                                $currentYear - 1 => $currentYear - 1,
                                $currentYear => $currentYear,
                                $currentYear + 1 => $currentYear + 1,
                            ];
                        })
                        ->default(now()->year)
                        ->required(),
                    TextInput::make('classes_per_schedule')
                        ->label('Jumlah Kelas / Hari')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->default(1),
                ])
                ->action(function (array $data) {
                    $classes = SchoolClass::where('is_active', true)
                        ->whereHas('academicYear', function ($query) {
                            $query->where('is_active', true);
                        })
                        ->pluck('id')
                        ->toArray();

                    if (empty($classes)) {
                        Notification::make()
                            ->title('Gagal')
                            ->body('Tidak ada kelas aktif pada tahun ajaran yang aktif.')
                            ->danger()
                            ->send();
                        return;
                    }

                    shuffle($classes);

                    $classCount = count($classes);
                    $currentIndex = 0;
                    $classesPerSchedule = (int) $data['classes_per_schedule'];

                    $month = (int) $data['month'];
                    $year = (int) $data['year'];
                    $selectedDays = array_map('intval', $data['selected_days']);

                    $startDate = Carbon::create($year, $month, 1);
                    $daysInMonth = $startDate->daysInMonth;

                    $agenda = Agenda::find($data['agenda_id']);
                    $agendaName = $agenda->name ?? 'Kegiatan';
                    $targetGender = strtolower(str_replace(' ', '_', $agenda->target_gender ?? 'all'));
                    $targetReligion = strtolower(str_replace(' ', '_', $agenda->target_religion ?? 'all'));

                    $scheduledClassIds = [];

                    // Mengambil tanggal libur dan memformat ke dalam array string (Y-m-d)
                    $holidays = Holiday::whereYear('date', $year)
                        ->whereMonth('date', $month)
                        ->pluck('date')
                        ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                        ->toArray();

                    DB::beginTransaction();
                    try {
                        for ($day = 1; $day <= $daysInMonth; $day++) {
                            $currentDate = Carbon::create($year, $month, $day);

                            // Guard Clause: Lewati jika hari tersebut adalah hari libur
                            if (in_array($currentDate->format('Y-m-d'), $holidays)) {
                                continue;
                            }

                            if (in_array($currentDate->dayOfWeekIso, $selectedDays)) {
                                $schedule = FridaySchedule::create([
                                    'date' => $currentDate->format('Y-m-d'),
                                    'agenda_id' => $data['agenda_id'],
                                ]);

                                $syncClasses = [];
                                for ($i = 0; $i < $classesPerSchedule; $i++) {
                                    $classId = $classes[$currentIndex];
                                    $syncClasses[] = $classId;
                                    $scheduledClassIds[] = $classId;

                                    $currentIndex++;
                                    if ($currentIndex >= $classCount) {
                                        $currentIndex = 0;
                                    }
                                }

                                $schedule->classes()->syncWithoutDetaching($syncClasses);
                            }
                        }

                        DB::commit();

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Jadwal bulanan berhasil digenerate, melewati hari libur terkait.')
                            ->success()
                            ->send();

                        // Kirim notifikasi sekaligus (Batch) agar tidak spam
                        $uniqueClasses = array_unique($scheduledClassIds);
                        $monthName = Carbon::create()->month($month)->translatedFormat('F');

                        foreach ($uniqueClasses as $cId) {
                            $classIdStr = strtolower(str_replace(' ', '_', $cId));

                            // Penentuan Topic
                            if ($targetGender === 'all' && $targetReligion === 'all') {
                                $topicName = "class_" . $classIdStr; 
                            } elseif ($targetGender !== 'all' && $targetReligion === 'all') {
                                $topicName = "class_" . $classIdStr . "_" . $targetGender;
                            } elseif ($targetGender === 'all' && $targetReligion !== 'all') {
                                $topicName = "class_" . $classIdStr . "_" . $targetReligion;
                            } else {
                                $topicName = "class_" . $classIdStr . "_" . $targetGender . "_" . $targetReligion;
                            }

                            self::sendNotificationToTopic(
                                $topicName,
                                "Jadwal $agendaName Bulanan 📅",
                                "Jadwal kegiatan untuk bulan $monthName $year telah dibagikan. Pastikan kamu mengecek kehadiranmu!"
                            );
                        }

                    } catch (\Exception $e) {
                        DB::rollBack();
                        Notification::make()
                            ->title('Terjadi Kesalahan')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            CreateAction::make(),
        ];
    }

    private static function getGoogleAccessToken()
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

    public static function sendNotificationToTopic($topic, $title, $body)
    {
        try {
            $accessToken = self::getGoogleAccessToken();
            
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
