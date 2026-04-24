<style>
    .es-label { font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
    .dark .es-label { color: #9ca3af; }

    .es-card {
        display: flex; align-items: flex-start; padding: 0.75rem 0.85rem;
        border-radius: 0.5rem; background: #eef2ff; border: 1px solid #c7d2fe;
    }
    .dark .es-card { background: rgba(99,102,241,0.08); border-color: rgba(99,102,241,0.2); }

    .es-card-icon {
        display: flex; align-items: center; justify-content: center;
        width: 2rem; height: 2rem; border-radius: 0.375rem; flex-shrink: 0;
        background: #6366f1; margin-right: 0.65rem;
    }
    .es-card-icon svg { color: white; }

    .es-agenda { font-weight: 700; font-size: 0.85rem; color: #312e81; }
    .dark .es-agenda { color: #c7d2fe; }

    .es-classes { font-size: 0.7rem; color: #4338ca; margin-top: 0.2rem; display: flex; flex-wrap: wrap; gap: 0.25rem; }
    .dark .es-classes { color: #a5b4fc; }

    .es-class-chip {
        display: inline-block; padding: 0.1rem 0.35rem; border-radius: 0.25rem;
        background: rgba(99,102,241,0.15); font-weight: 600; font-size: 0.65rem;
    }
    .dark .es-class-chip { background: rgba(99,102,241,0.2); }

    .es-desc { font-size: 0.7rem; color: #6366f1; margin-top: 0.15rem; font-style: italic; opacity: 0.8; }
    .dark .es-desc { color: #818cf8; }

    .es-actions {
        display: flex; align-items: center; gap: 0.3rem; margin-left: auto; flex-shrink: 0; padding-left: 0.5rem;
    }

    .es-btn {
        display: inline-flex; align-items: center; gap: 0.25rem;
        padding: 0.3rem 0.5rem; border-radius: 0.375rem; border: 1px solid #c7d2fe;
        background: white; cursor: pointer; font-size: 0.7rem;
        font-weight: 600; transition: all 0.15s; text-decoration: none;
    }
    .es-btn--view { color: #4f46e5; }
    .es-btn--view:hover { background: #4f46e5; color: white; border-color: #4f46e5; }
    .dark .es-btn--view { background: rgba(99,102,241,0.1); border-color: rgba(99,102,241,0.25); color: #818cf8; }
    .dark .es-btn--view:hover { background: #4f46e5; color: white; border-color: #4f46e5; }

    .es-btn--edit { color: #059669; border-color: #a7f3d0; }
    .es-btn--edit:hover { background: #059669; color: white; border-color: #059669; }
    .dark .es-btn--edit { background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.25); color: #34d399; }
    .dark .es-btn--edit:hover { background: #059669; color: white; border-color: #059669; }

    .es-btn--del { color: #dc2626; border-color: #fca5a5; }
    .es-btn--del:hover { background: #dc2626; color: white; border-color: #dc2626; }
    .dark .es-btn--del { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.25); color: #f87171; }
    .dark .es-btn--del:hover { background: #dc2626; color: white; border-color: #dc2626; }

    .es-holiday-card {
        display: flex; align-items: center; padding: 0.6rem 0.85rem;
        border-radius: 0.5rem; background: #fef2f2; border: 1px solid #fecaca;
    }
    .dark .es-holiday-card { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.2); }

    .es-holiday-name { font-weight: 600; font-size: 0.85rem; color: #991b1b; }
    .dark .es-holiday-name { color: #fca5a5; }

    .es-holiday-desc { font-size: 0.7rem; color: #b91c1c; margin-top: 0.1rem; opacity: 0.8; }
    .dark .es-holiday-desc { color: #f87171; }

    .es-empty { text-align: center; padding: 1rem; color: #9ca3af; font-size: 0.8rem; }
    .dark .es-empty { color: #6b7280; }

    .es-divider { border: none; border-top: 1px solid #e5e7eb; margin: 0.75rem 0; }
    .dark .es-divider { border-top-color: rgba(255,255,255,0.1); }
</style>

@php
    $viewData = $getViewData();
    $schedules = $viewData['schedules'];
    $holidays = $viewData['holidays'];
    $scheduleItems = is_callable($schedules) ? $schedules() : $schedules;
    $holidayItems = is_callable($holidays) ? $holidays() : $holidays;
@endphp

{{-- Holidays on this date --}}
@if ($holidayItems->isNotEmpty())
    <div style="margin-bottom: 0.75rem;">
        <div class="es-label">🚫 Hari Libur</div>
        <div style="display: flex; flex-direction: column; gap: 0.4rem;">
            @foreach ($holidayItems as $holiday)
                <div class="es-holiday-card">
                    <div style="flex: 1; min-width: 0;">
                        <div class="es-holiday-name">{{ $holiday->name }}</div>
                        @if ($holiday->description)
                            <div class="es-holiday-desc">{{ $holiday->description }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <hr class="es-divider">
@endif

{{-- Schedules on this date --}}
@if ($scheduleItems->isNotEmpty())
    <div>
        <div class="es-label">Jadwal Terdaftar ({{ $scheduleItems->count() }})</div>
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            @foreach ($scheduleItems as $schedule)
                <div class="es-card">
                    <div class="es-card-icon">
                        <x-heroicon-s-calendar-days style="width: 1rem; height: 1rem;" />
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="es-agenda">{{ $schedule->agenda->name ?? '-' }}</div>
                        <div class="es-classes">
                            @foreach ($schedule->classes as $c)
                                <span class="es-class-chip">{{ $c->name }}</span>
                            @endforeach
                        </div>
                        @if ($schedule->description)
                            <div class="es-desc">{{ $schedule->description }}</div>
                        @endif
                    </div>
                    <div class="es-actions">
                        <a href="{{ \App\Filament\Resources\Schedules\ScheduleResource::getUrl('view', ['record' => $schedule->id]) }}" class="es-btn es-btn--view" title="Lihat">
                            <x-heroicon-o-eye style="width: 0.8rem; height: 0.8rem;" />
                        </a>
                        <a href="{{ \App\Filament\Resources\Schedules\ScheduleResource::getUrl('edit', ['record' => $schedule->id]) }}" class="es-btn es-btn--edit" title="Edit">
                            <x-heroicon-o-pencil-square style="width: 0.8rem; height: 0.8rem;" />
                        </a>
                        <button
                            type="button"
                            x-data
                            x-on:click="$dispatch('confirm-delete', {
                                title: 'Hapus Jadwal',
                                message: 'Yakin ingin menghapus jadwal \'{{ $schedule->agenda->name ?? '' }}\' ini?',
                                action: () => $wire.deleteScheduleFromModal({{ $schedule->id }})
                            })"
                            class="es-btn es-btn--del"
                            title="Hapus">
                            <x-heroicon-o-trash style="width: 0.8rem; height: 0.8rem;" />
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="es-empty">
        <p>Belum ada jadwal pada tanggal ini.</p>
    </div>
@endif
