<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use App\Models\Agenda;
use App\Models\FridaySchedule;
use App\Models\SchoolClass;
use App\Models\Holiday;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class ListSchedules extends Page
{
    protected static string $resource = ScheduleResource::class;

    protected string $view = 'filament.resources.schedules.pages.list-schedules';

    public int $currentMonth;
    public int $currentYear;
    public ?string $selectedDate = null;

    /** @var array<int> IDs of schedules selected for bulk actions */
    public array $selectedScheduleIds = [];

    public function mount(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function getTitle(): string
    {
        return 'Kalender Jadwal';
    }

    protected function getHeaderActions(): array
    {
        return [
            // ── Hidden modal: triggered by clicking a calendar cell ──
            Action::make('manageDay')
                ->label('Detail Hari')
                ->modalHeading(function () {
                    if ($this->selectedDate) {
                        return '📅 ' . Carbon::parse($this->selectedDate)->translatedFormat('l, d F Y');
                    }
                    return 'Detail Hari';
                })
                ->modalSubmitAction(false)
                ->form([
                    ViewField::make('existingSchedules')
                        ->label('')
                        ->view('filament.resources.schedules.pages.existing-schedules')
                        ->viewData([
                            'schedules' => fn () => $this->getSelectedDateSchedules(),
                            'holidays' => fn () => $this->getSelectedDateHolidays(),
                        ]),
                ])
                ->modalCancelActionLabel('Tutup')
                ->link()
                ->extraAttributes(['style' => 'display:none']),

            // ── Generate Bulanan Auto ──
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

                        // Refresh calendar data
                        unset($this->monthlySchedules, $this->monthlyHolidays, $this->calendarData, $this->schedulesList);

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
            Action::make('createSchedule')
                ->label('Tambah Jadwal')
                ->icon('heroicon-o-plus')
                ->url(fn () => ScheduleResource::getUrl('create')),
        ];
    }

    // ─── Calendar Cell Click ─────────────────────────────────────
    public function openDayModal(string $date): void
    {
        $this->selectedDate = $date;
        $this->mountAction('manageDay');
    }

    public function getSelectedDateSchedules(): Collection
    {
        if (!$this->selectedDate) {
            return new Collection();
        }

        return FridaySchedule::with('agenda', 'classes')
            ->whereDate('date', $this->selectedDate)
            ->orderBy('id')
            ->get();
    }

    public function getSelectedDateHolidays(): Collection
    {
        if (!$this->selectedDate) {
            return new Collection();
        }

        return Holiday::whereDate('date', $this->selectedDate)->get();
    }

    // ─── Delete single schedule (from modal) ─────────────────────
    public function deleteScheduleFromModal(int $id): void
    {
        $schedule = FridaySchedule::findOrFail($id);
        $schedule->classes()->detach();
        $schedule->delete();

        unset($this->monthlySchedules, $this->monthlyHolidays, $this->calendarData, $this->schedulesList);

        Notification::make()
            ->title('Berhasil')
            ->body('Jadwal berhasil dihapus.')
            ->success()
            ->send();
    }

    // ─── Bulk Selection ──────────────────────────────────────────
    public function toggleSchedule(int $id): void
    {
        if (in_array($id, $this->selectedScheduleIds)) {
            $this->selectedScheduleIds = array_values(
                array_diff($this->selectedScheduleIds, [$id])
            );
        } else {
            $this->selectedScheduleIds[] = $id;
        }
    }

    public function toggleAllSchedules(): void
    {
        $allIds = $this->schedulesList->pluck('id')->toArray();

        if (count($this->selectedScheduleIds) === count($allIds)) {
            $this->selectedScheduleIds = [];
        } else {
            $this->selectedScheduleIds = $allIds;
        }
    }

    public function bulkDeleteSchedules(): void
    {
        if (empty($this->selectedScheduleIds)) {
            Notification::make()
                ->title('Peringatan')
                ->body('Pilih minimal satu jadwal untuk dihapus.')
                ->warning()
                ->send();
            return;
        }

        $count = count($this->selectedScheduleIds);

        DB::table('schedule_classes')
            ->whereIn('schedule_id', $this->selectedScheduleIds)
            ->delete();

        FridaySchedule::whereIn('id', $this->selectedScheduleIds)->delete();

        $this->selectedScheduleIds = [];
        unset($this->monthlySchedules, $this->monthlyHolidays, $this->calendarData, $this->schedulesList);

        Notification::make()
            ->title('Berhasil')
            ->body("{$count} jadwal berhasil dihapus.")
            ->success()
            ->send();
    }

    // ─── Navigation ──────────────────────────────────────────────
    public function previousMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->selectedScheduleIds = [];
        unset($this->monthlySchedules, $this->monthlyHolidays, $this->calendarData, $this->schedulesList);
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->selectedScheduleIds = [];
        unset($this->monthlySchedules, $this->monthlyHolidays, $this->calendarData, $this->schedulesList);
    }

    // ─── Single delete (from list) ───────────────────────────────
    public function deleteSchedule(int $id): void
    {
        $schedule = FridaySchedule::findOrFail($id);
        $schedule->classes()->detach();
        $schedule->delete();

        $this->selectedScheduleIds = array_values(
            array_diff($this->selectedScheduleIds, [$id])
        );
        unset($this->monthlySchedules, $this->monthlyHolidays, $this->calendarData, $this->schedulesList);

        Notification::make()
            ->title('Berhasil')
            ->body('Jadwal berhasil dihapus.')
            ->success()
            ->send();
    }

    // ─── Computed (Single Source of Truth) ─────────────────────────
    #[Computed]
    public function monthlySchedules(): Collection
    {
        return FridaySchedule::with('agenda', 'classes')
            ->whereYear('date', $this->currentYear)
            ->whereMonth('date', $this->currentMonth)
            ->orderBy('date')
            ->get();
    }

    #[Computed]
    public function monthlyHolidays(): Collection
    {
        return Holiday::whereYear('date', $this->currentYear)
            ->whereMonth('date', $this->currentMonth)
            ->orderBy('date')
            ->get();
    }

    #[Computed]
    public function calendarData(): array
    {
        $schedules = $this->monthlySchedules
            ->groupBy(fn ($s) => Carbon::parse($s->date)->format('Y-m-d'));

        $holidays = $this->monthlyHolidays
            ->groupBy(fn ($h) => Carbon::parse($h->date)->format('Y-m-d'));

        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $startDayOfWeek = $startOfMonth->dayOfWeekIso;
        $daysInMonth = $startOfMonth->daysInMonth;

        $weeks = [];
        $currentWeek = array_fill(0, 7, null);
        $dayIndex = $startDayOfWeek - 1;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = Carbon::create($this->currentYear, $this->currentMonth, $day)->format('Y-m-d');

            $daySchedules = $schedules->has($dateStr) ? $schedules[$dateStr]->toArray() : [];
            $dayHolidays = $holidays->has($dateStr) ? $holidays[$dateStr]->toArray() : [];

            $currentWeek[$dayIndex] = [
                'day' => $day,
                'date' => $dateStr,
                'isToday' => $dateStr === now()->format('Y-m-d'),
                'isHoliday' => count($dayHolidays) > 0,
                'holidays' => $dayHolidays,
                'schedules' => $daySchedules,
            ];

            $dayIndex++;

            if ($dayIndex >= 7) {
                $weeks[] = $currentWeek;
                $currentWeek = array_fill(0, 7, null);
                $dayIndex = 0;
            }
        }

        if ($dayIndex > 0) {
            $weeks[] = $currentWeek;
        }

        return $weeks;
    }

    #[Computed]
    public function monthName(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $months[$this->currentMonth] . ' ' . $this->currentYear;
    }

    #[Computed]
    public function schedulesList(): Collection
    {
        return $this->monthlySchedules;
    }

    // ─── FCM ─────────────────────────────────────────────────────
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
