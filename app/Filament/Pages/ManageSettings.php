<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSettings extends Page
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;
    protected static ?string $navigationLabel = 'Pengaturan';
    protected static ?string $title = 'Konfigurasi Sistem';
    protected  string $view = 'filament.pages.manage-settings';
    protected static string|UnitEnum|null $navigationGroup = "Pengaturan";
    protected static ?int $navigationSort = 10;
    

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            Setting::all()->pluck('value', 'key')->toArray()
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->inlineLabel()
            ->components([
                ComponentsSection::make('Lokasi Presensi (Geofencing)')
                    ->description('Atur koordinat pusat sekolah dan radius absen.')
                    ->schema([
                        TextInput::make('school_latitude')
                            ->label('Latitude Sekolah')
                            ->required(),
                        TextInput::make('school_longitude')
                            ->label('Longitude Sekolah')
                            ->required(),
                        TextInput::make('attendance_radius')
                            ->label('Radius Aman (Meter)')
                            ->numeric()
                            ->suffix('meter')
                            ->required(),
                    ])->columns(1),

                ComponentsSection::make('Waktu Operasional')
                    ->description('Atur jam berapa siswa mulai bisa absen.')
                    ->schema([
                        TimePicker::make('start_time')
                            ->label('Jam Buka Absen')
                            ->required(),
                        TimePicker::make('end_time')
                            ->label('Jam Tutup / Alpha Otomatis')
                            ->required(),
                    ])->columns(1),

                ComponentsSection::make('General Settings')
                ->description('Pengaturan dasar website dan aplikasi.')
                ->schema([
                    TextInput::make('site_name')
                        ->label('Nama Aplikasi')
                        ->required(),
                    TextInput::make('site_url')
                        ->label('URL Website')
                        ->url() 
                        ->placeholder('https://nama-sekolah.sch.id')
                        ->required(),
                    FileUpload::make('site_favicon')
                        ->label('Favicon / Logo')
                        ->image() 
                        ->disk('public')
                        ->visibility('public')
                        ->directory('settings') 
                        ->imageEditor(), 
                ])->columns(1)->columnSpanFull(),
            ])->columns(2);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Notification::make()
            ->title('Settings berhasil disimpan!')
            ->success()
            ->send();
    }
}