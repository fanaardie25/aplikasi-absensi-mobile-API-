<?php

namespace App\Filament\Resources\Holidays\Pages;

use App\Filament\Resources\Holidays\HolidayResource;
use App\Models\Holiday;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;

class ListHolidays extends Page
{
    protected static string $resource = HolidayResource::class;

    protected string $view = 'filament.resources.holidays.pages.list-holidays';

    public int $currentMonth;
    public int $currentYear;
    public ?string $selectedDate = null;

    public function mount(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function getTitle(): string
    {
        return 'Kalender Hari Libur';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageDay')
                ->label('Kelola Hari')
                ->modalHeading(function () {
                    if ($this->selectedDate) {
                        return '📅 ' . Carbon::parse($this->selectedDate)->translatedFormat('l, d F Y');
                    }
                    return 'Kelola Hari';
                })
                ->modalSubmitActionLabel('Simpan Libur')
                ->form([
                    ViewField::make('existingHolidays')
                        ->label('')
                        ->view('filament.resources.holidays.pages.existing-holidays')
                        ->viewData([
                            'holidays' => fn () => $this->getSelectedDateHolidays(),
                        ]),
                    TextInput::make('name')
                        ->label('Nama Libur Baru')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('cth: Hari Raya Idul Fitri'),
                    Textarea::make('description')
                        ->label('Keterangan')
                        ->maxLength(65535)
                        ->placeholder('Opsional'),
                ])
                ->action(function (array $data) {
                    if (!$this->selectedDate) {
                        return;
                    }

                    Holiday::create([
                        'name' => $data['name'],
                        'date' => $this->selectedDate,
                        'description' => $data['description'] ?? null,
                    ]);

                    unset($this->calendarData, $this->holidaysList);

                    Notification::make()
                        ->title('Berhasil')
                        ->body('Hari libur berhasil ditambahkan.')
                        ->success()
                        ->send();
                })
                ->modalCancelActionLabel('Tutup')
                ->link()
                ->extraAttributes(['style' => 'display:none']),
        ];
    }

    public function openDayModal(string $date): void
    {
        $this->selectedDate = $date;
        $this->mountAction('manageDay');
    }

    public function getSelectedDateHolidays(): Collection
    {
        if (!$this->selectedDate) {
            return new Collection();
        }

        return Holiday::whereDate('date', $this->selectedDate)
            ->orderBy('name')
            ->get();
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        unset($this->calendarData, $this->holidaysList);
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        unset($this->calendarData, $this->holidaysList);
    }

    public function deleteHoliday(int $id): void
    {
        Holiday::findOrFail($id)->delete();

        unset($this->calendarData, $this->holidaysList);

        Notification::make()
            ->title('Berhasil')
            ->body('Hari libur berhasil dihapus.')
            ->success()
            ->send();
    }

    #[Computed]
    public function calendarData(): array
    {
        $holidays = Holiday::whereYear('date', $this->currentYear)
            ->whereMonth('date', $this->currentMonth)
            ->orderBy('date')
            ->get()
            ->groupBy(fn ($h) => Carbon::parse($h->date)->format('Y-m-d'));

        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $startDayOfWeek = $startOfMonth->dayOfWeekIso;
        $daysInMonth = $startOfMonth->daysInMonth;

        $weeks = [];
        $currentWeek = array_fill(0, 7, null);
        $dayIndex = $startDayOfWeek - 1;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = Carbon::create($this->currentYear, $this->currentMonth, $day)->format('Y-m-d');

            $currentWeek[$dayIndex] = [
                'day' => $day,
                'date' => $dateStr,
                'isToday' => $dateStr === now()->format('Y-m-d'),
                'holidays' => $holidays->has($dateStr) ? $holidays[$dateStr]->toArray() : [],
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
    public function holidaysList(): Collection
    {
        return Holiday::whereYear('date', $this->currentYear)
            ->whereMonth('date', $this->currentMonth)
            ->orderBy('date')
            ->get();
    }
}
