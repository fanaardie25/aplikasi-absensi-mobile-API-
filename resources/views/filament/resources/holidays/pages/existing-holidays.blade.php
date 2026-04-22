<style>
    .eh-label { font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
    .dark .eh-label { color: #9ca3af; }

    .eh-card {
        display: flex; align-items: center; padding: 0.7rem 0.85rem;
        border-radius: 0.5rem; background: #fef2f2; border: 1px solid #fecaca;
    }
    .dark .eh-card { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.2); }

    .eh-name { font-weight: 600; font-size: 0.875rem; color: #991b1b; }
    .dark .eh-name { color: #fca5a5; }

    .eh-desc { font-size: 0.75rem; color: #b91c1c; margin-top: 0.1rem; opacity: 0.8; }
    .dark .eh-desc { color: #f87171; }

    .eh-del-btn {
        margin-left: 0.5rem; display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.3rem 0.6rem; border-radius: 0.375rem; border: 1px solid #fca5a5;
        background: white; cursor: pointer; color: #dc2626; font-size: 0.75rem;
        font-weight: 600; transition: all 0.15s;
    }
    .eh-del-btn:hover { background: #dc2626; color: white; border-color: #dc2626; }
    .dark .eh-del-btn { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.25); color: #f87171; }
    .dark .eh-del-btn:hover { background: #dc2626; color: white; border-color: #dc2626; }

    .eh-divider { border: none; border-top: 1px solid #e5e7eb; margin: 0.75rem 0; }
    .dark .eh-divider { border-top-color: rgba(255,255,255,0.1); }
</style>

@php
    $holidays = $getViewData()['holidays'];
    $items = is_callable($holidays) ? $holidays() : $holidays;
@endphp

@if ($items->isNotEmpty())
    <div style="margin-bottom: 1rem;">
        <div class="eh-label">Libur yang sudah terdaftar</div>
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            @foreach ($items as $holiday)
                <div class="eh-card">
                    <div style="flex: 1; min-width: 0;">
                        <div class="eh-name">{{ $holiday->name }}</div>
                        @if ($holiday->description)
                            <div class="eh-desc">{{ $holiday->description }}</div>
                        @endif
                    </div>
                    <button
                        type="button"
                        wire:click="deleteHoliday({{ $holiday->id }})"
                        wire:confirm="Yakin ingin menghapus '{{ $holiday->name }}'?"
                        class="eh-del-btn">
                        <x-heroicon-o-trash style="width: 0.85rem; height: 0.85rem;" />
                        Hapus
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    <hr class="eh-divider">
    <div class="eh-label" style="margin-bottom: 0.25rem;">Atau tambah libur baru</div>
@endif
