<x-filament-panels::page>
    <style>
        :root {
            --cal-primary: #10b981;
            --cal-primary-light: #d1fae5;
            --cal-primary-dark: #059669;
            --cal-primary-subtle: #ecfdf5;
            --cal-danger: #ef4444;
            --cal-danger-light: #fef2f2;
            --cal-danger-border: #fecaca;
        }
        .dark {
            --cal-primary: #34d399;
            --cal-primary-light: rgba(16,185,129,0.12);
            --cal-primary-dark: #6ee7b7;
            --cal-primary-subtle: rgba(16,185,129,0.06);
            --cal-danger: #f87171;
            --cal-danger-light: rgba(239,68,68,0.08);
            --cal-danger-border: rgba(239,68,68,0.2);
        }

        /* ===== Navigation ===== */
        .cal-nav {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.75rem 1rem; margin-bottom: 1rem;
        }
        .cal-nav-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; border: none;
            background: var(--cal-primary-light); color: var(--cal-primary-dark);
            cursor: pointer; transition: all 0.2s;
        }
        .cal-nav-btn:hover { background: var(--cal-primary); color: white; transform: scale(1.05); }
        .cal-title { font-size: 1.15rem; font-weight: 700; color: #1f2937; }
        .dark .cal-title { color: #f3f4f6; }

        /* ===== Grid Container ===== */
        .cal-wrap {
            border-radius: 1rem; overflow: hidden;
            border: 1px solid #e5e7eb;
            background: white;
        }
        .dark .cal-wrap {
            border-color: rgba(255,255,255,0.06);
            background: rgba(255,255,255,0.02);
        }

        /* ===== Day Headers ===== */
        .cal-header {
            display: grid; grid-template-columns: repeat(7, 1fr);
            background: var(--cal-primary);
        }
        .cal-header-cell {
            padding: 0.6rem 0; text-align: center;
            font-size: 0.7rem; font-weight: 700; color: white;
            text-transform: uppercase; letter-spacing: 0.08em;
        }

        /* ===== Week Row ===== */
        .cal-week {
            display: grid; grid-template-columns: repeat(7, 1fr);
        }

        /* ===== Cell ===== */
        .cal-cell {
            min-height: 5.5rem; padding: 0.4rem; position: relative;
            border-top: 1px solid #f3f4f6; border-right: 1px solid #f3f4f6;
            cursor: pointer; transition: all 0.15s; background: white;
        }
        .dark .cal-cell {
            background: transparent; border-color: rgba(255,255,255,0.04);
        }
        .cal-cell:nth-child(7n) { border-right: none; }
        .cal-cell:hover {
            background: var(--cal-primary-subtle);
            box-shadow: inset 0 0 0 2px var(--cal-primary);
            z-index: 1;
        }

        .cal-cell--empty {
            cursor: default; background: #fafafa;
        }
        .dark .cal-cell--empty { background: rgba(255,255,255,0.01); }
        .cal-cell--empty:hover { background: #fafafa; box-shadow: none; }
        .dark .cal-cell--empty:hover { background: rgba(255,255,255,0.01); }

        .cal-cell--today { background: var(--cal-primary-subtle); }
        .cal-cell--holiday { background: var(--cal-danger-light); }

        /* ===== Day Number ===== */
        .cal-day {
            width: 1.6rem; height: 1.6rem; border-radius: 9999px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 600; color: #4b5563;
            margin-bottom: 0.2rem;
        }
        .dark .cal-day { color: #d1d5db; }
        .cal-day--today {
            background: var(--cal-primary); color: white !important;
            font-weight: 700;
        }

        /* ===== Holiday Badge ===== */
        .cal-badge {
            padding: 0.1rem 0.35rem; border-radius: 0.25rem; margin-bottom: 0.15rem;
            background: var(--cal-danger); font-size: 0.58rem; line-height: 1.4;
            color: white; font-weight: 600; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis; display: block;
        }

        /* ===== List Section ===== */
        .hlist {
            margin-top: 1.25rem; border-radius: 1rem; overflow: hidden;
            border: 1px solid #e5e7eb; background: white;
        }
        .dark .hlist { border-color: rgba(255,255,255,0.06); background: rgba(255,255,255,0.02); }

        .hlist-header {
            padding: 0.85rem 1.25rem; display: flex; align-items: center; gap: 0.5rem;
            border-bottom: 1px solid #f3f4f6;
        }
        .dark .hlist-header { border-bottom-color: rgba(255,255,255,0.05); }

        .hlist-title { font-weight: 600; font-size: 0.9rem; color: #111827; margin: 0; }
        .dark .hlist-title { color: #f3f4f6; }

        .hlist-count {
            margin-left: auto; background: var(--cal-primary-light);
            color: var(--cal-primary-dark); font-weight: 700; font-size: 0.7rem;
            padding: 0.15rem 0.55rem; border-radius: 9999px;
        }

        .hlist-item {
            display: flex; align-items: center; padding: 0.7rem 1.25rem;
            border-bottom: 1px solid #f9fafb; cursor: pointer;
            transition: background 0.15s; background: white;
        }
        .hlist-item:last-child { border-bottom: none; }
        .hlist-item:hover { background: var(--cal-primary-subtle); }
        .dark .hlist-item { background: transparent; border-bottom-color: rgba(255,255,255,0.03); }
        .dark .hlist-item:hover { background: var(--cal-primary-subtle); }

        .hlist-icon {
            display: flex; align-items: center; justify-content: center;
            width: 2.25rem; height: 2.25rem; border-radius: 0.5rem;
            background: var(--cal-danger-light); border: 1px solid var(--cal-danger-border);
            margin-right: 0.75rem; flex-shrink: 0;
        }
        .hlist-icon span { font-size: 0.7rem; font-weight: 700; color: var(--cal-danger); }

        .hlist-name { font-weight: 600; font-size: 0.85rem; color: #111827; }
        .dark .hlist-name { color: #f3f4f6; }

        .hlist-desc { font-size: 0.7rem; color: #6b7280; margin-top: 0.1rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dark .hlist-desc { color: #9ca3af; }

        .hlist-date { font-size: 0.7rem; color: #9ca3af; white-space: nowrap; margin-left: 0.75rem; }
        .dark .hlist-date { color: #6b7280; }

        /* ===== Empty State ===== */
        .cal-empty {
            margin-top: 1.25rem; border-radius: 1rem;
            border: 1px solid #e5e7eb; border-style: dashed;
            padding: 2.5rem 1.5rem; text-align: center; background: white;
        }
        .dark .cal-empty { background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.08); }
        .cal-empty-icon { font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.6; }
        .cal-empty-title { font-weight: 600; color: #4b5563; margin: 0 0 0.25rem; font-size: 0.9rem; }
        .dark .cal-empty-title { color: #d1d5db; }
        .cal-empty-desc { font-size: 0.8rem; color: #9ca3af; margin: 0; }
        .dark .cal-empty-desc { color: #6b7280; }
    </style>

    <div>
        {{-- Navigation --}}
        <div class="cal-nav">
            <button wire:click="previousMonth" type="button" class="cal-nav-btn">
                <x-heroicon-m-chevron-left class="w-4 h-4" />
            </button>
            <h2 class="cal-title">{{ $this->monthName }}</h2>
            <button wire:click="nextMonth" type="button" class="cal-nav-btn">
                <x-heroicon-m-chevron-right class="w-4 h-4" />
            </button>
        </div>

        {{-- Calendar --}}
        <div class="cal-wrap">
            <div class="cal-header">
                @foreach (['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MIN'] as $d)
                    <div class="cal-header-cell">{{ $d }}</div>
                @endforeach
            </div>

            @foreach ($this->calendarData as $week)
                <div class="cal-week">
                    @foreach ($week as $cell)
                        @if ($cell === null)
                            <div class="cal-cell cal-cell--empty"></div>
                        @else
                            @php
                                $cls = 'cal-cell';
                                if ($cell['isToday']) $cls .= ' cal-cell--today';
                                elseif (count($cell['holidays']) > 0) $cls .= ' cal-cell--holiday';
                            @endphp
                            <div wire:click="openDayModal('{{ $cell['date'] }}')" class="{{ $cls }}">
                                <div class="cal-day {{ $cell['isToday'] ? 'cal-day--today' : '' }}">
                                    {{ $cell['day'] }}
                                </div>
                                @foreach ($cell['holidays'] as $h)
                                    <span class="cal-badge" title="{{ $h['name'] }}">{{ $h['name'] }}</span>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- List --}}
        @php $list = $this->holidaysList; @endphp
        @if ($list->isNotEmpty())
            <div class="hlist">
                <div class="hlist-header">
                    <span style="font-size:1rem"></span>
                    <h3 class="hlist-title">Daftar Hari Libur — {{ $this->monthName }}</h3>
                    <span class="hlist-count">{{ $list->count() }} hari</span>
                </div>
                @foreach ($list as $holiday)
                    <div wire:click="openDayModal('{{ \Carbon\Carbon::parse($holiday->date)->format('Y-m-d') }}')" class="hlist-item">
                        <div class="hlist-icon">
                            <span>{{ \Carbon\Carbon::parse($holiday->date)->format('d') }}</span>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="hlist-name">{{ $holiday->name }}</div>
                            @if ($holiday->description)
                                <div class="hlist-desc">{{ $holiday->description }}</div>
                            @endif
                        </div>
                        <div class="hlist-date">{{ \Carbon\Carbon::parse($holiday->date)->translatedFormat('l, d M Y') }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="cal-empty">
                <div class="cal-empty-icon">📅</div>
                <p class="cal-empty-title">Tidak ada hari libur</p>
                <p class="cal-empty-desc">Belum ada hari libur yang terdaftar untuk bulan {{ $this->monthName }}.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
