<x-filament-panels::page>
    <style>
        :root {
            --scal-primary: #10b981;
            --scal-primary-light: #d1fae5;
            --scal-primary-dark: #059669;
            --scal-primary-subtle: #ecfdf5;
            --scal-accent: #6366f1;
            --scal-accent-light: #eef2ff;
            --scal-accent-dark: #4f46e5;
            --scal-danger: #ef4444;
            --scal-danger-light: #fef2f2;
            --scal-danger-border: #fecaca;
            --scal-warn: #f59e0b;
            --scal-warn-light: #fffbeb;
        }
        .dark {
            --scal-primary: #34d399;
            --scal-primary-light: rgba(16,185,129,0.12);
            --scal-primary-dark: #6ee7b7;
            --scal-primary-subtle: rgba(16,185,129,0.06);
            --scal-accent: #818cf8;
            --scal-accent-light: rgba(99,102,241,0.1);
            --scal-accent-dark: #a5b4fc;
            --scal-danger: #f87171;
            --scal-danger-light: rgba(239,68,68,0.08);
            --scal-danger-border: rgba(239,68,68,0.2);
            --scal-warn: #fbbf24;
            --scal-warn-light: rgba(245,158,11,0.1);
        }

        /* ===== Navigation ===== */
        .scal-nav {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.75rem 1rem; margin-bottom: 1rem;
        }
        .scal-nav-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; border: none;
            background: var(--scal-primary-light); color: var(--scal-primary-dark);
            cursor: pointer; transition: all 0.2s;
        }
        .scal-nav-btn:hover { background: var(--scal-primary); color: white; transform: scale(1.05); }
        .scal-title { font-size: 1.15rem; font-weight: 700; color: #1f2937; }
        .dark .scal-title { color: #f3f4f6; }

        /* ===== Grid Container ===== */
        .scal-wrap {
            border-radius: 1rem; overflow: hidden;
            border: 1px solid #e5e7eb;
            background: white;
        }
        .dark .scal-wrap {
            border-color: rgba(255,255,255,0.06);
            background: rgba(255,255,255,0.02);
        }

        /* ===== Day Headers ===== */
        .scal-header {
            display: grid; grid-template-columns: repeat(7, 1fr);
            background: var(--scal-primary);
        }
        .scal-header-cell {
            padding: 0.6rem 0; text-align: center;
            font-size: 0.7rem; font-weight: 700; color: white;
            text-transform: uppercase; letter-spacing: 0.08em;
        }

        /* ===== Week Row ===== */
        .scal-week {
            display: grid; grid-template-columns: repeat(7, 1fr);
        }

        /* ===== Cell ===== */
        .scal-cell {
            min-height: 6rem; padding: 0.4rem; position: relative;
            border-top: 1px solid #f3f4f6; border-right: 1px solid #f3f4f6;
            cursor: pointer; transition: all 0.15s; background: white;
        }
        .dark .scal-cell {
            background: transparent; border-color: rgba(255,255,255,0.04);
        }
        .scal-cell:nth-child(7n) { border-right: none; }
        .scal-cell:hover {
            background: var(--scal-primary-subtle);
            box-shadow: inset 0 0 0 2px var(--scal-primary);
            z-index: 1;
        }

        .scal-cell--empty {
            cursor: default; background: #fafafa;
        }
        .dark .scal-cell--empty { background: rgba(255,255,255,0.01); }
        .scal-cell--empty:hover { background: #fafafa; box-shadow: none; }
        .dark .scal-cell--empty:hover { background: rgba(255,255,255,0.01); }

        .scal-cell--today { background: var(--scal-primary-subtle); }
        .scal-cell--holiday { background: var(--scal-danger-light); }

        /* ===== Day Number ===== */
        .scal-day {
            width: 1.6rem; height: 1.6rem; border-radius: 9999px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 600; color: #4b5563;
            margin-bottom: 0.2rem;
        }
        .dark .scal-day { color: #d1d5db; }
        .scal-day--today {
            background: var(--scal-primary); color: white !important;
            font-weight: 700;
        }

        /* ===== Schedule count indicator ===== */
        .scal-count-dot {
            position: absolute; top: 0.3rem; right: 0.3rem;
            min-width: 1.1rem; height: 1.1rem; border-radius: 9999px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.55rem; font-weight: 700; color: white;
            background: var(--scal-accent);
        }

        /* ===== Schedule Badge ===== */
        .scal-badge {
            padding: 0.1rem 0.35rem; border-radius: 0.25rem; margin-bottom: 0.15rem;
            font-size: 0.55rem; line-height: 1.4;
            font-weight: 600; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis; display: block;
        }
        .scal-badge--agenda { background: var(--scal-accent); color: white; }
        .scal-badge--class { background: var(--scal-primary); color: white; font-weight: 500; font-size: 0.5rem; }
        .scal-badge--holiday { background: var(--scal-danger); color: white; }
        .scal-badge--more { background: #e5e7eb; color: #6b7280; font-size: 0.5rem; text-align: center; }
        .dark .scal-badge--more { background: rgba(255,255,255,0.08); color: #9ca3af; }

        /* ===== Custom Confirm Modal ===== */
        .confirm-overlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            animation: confirmFadeIn 0.15s ease-out;
        }
        @keyframes confirmFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes confirmSlideIn {
            from { opacity: 0; transform: scale(0.95) translateY(8px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .confirm-box {
            background: white; border-radius: 1rem; padding: 1.5rem;
            width: 100%; max-width: 26rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            animation: confirmSlideIn 0.2s ease-out;
        }
        .dark .confirm-box { background: #1f2937; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }

        .confirm-icon-wrap {
            width: 3rem; height: 3rem; border-radius: 9999px;
            background: var(--scal-danger-light); border: 1px solid var(--scal-danger-border);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 0.85rem;
        }
        .confirm-icon-wrap svg { color: var(--scal-danger); width: 1.4rem; height: 1.4rem; }

        .confirm-title {
            font-size: 1rem; font-weight: 700; color: #111827;
            text-align: center; margin-bottom: 0.35rem;
        }
        .dark .confirm-title { color: #f3f4f6; }

        .confirm-msg {
            font-size: 0.85rem; color: #6b7280; text-align: center;
            margin-bottom: 1.25rem; line-height: 1.5;
        }
        .dark .confirm-msg { color: #9ca3af; }

        .confirm-actions {
            display: flex; gap: 0.6rem; justify-content: center;
        }
        .confirm-btn {
            padding: 0.5rem 1.25rem; border-radius: 0.5rem; font-size: 0.8rem;
            font-weight: 600; cursor: pointer; transition: all 0.15s; border: none;
        }
        .confirm-btn--cancel {
            background: #f3f4f6; color: #374151;
        }
        .confirm-btn--cancel:hover { background: #e5e7eb; }
        .dark .confirm-btn--cancel { background: rgba(255,255,255,0.08); color: #d1d5db; }
        .dark .confirm-btn--cancel:hover { background: rgba(255,255,255,0.12); }

        .confirm-btn--danger {
            background: var(--scal-danger); color: white;
        }
        .confirm-btn--danger:hover { background: #dc2626; transform: scale(1.02); }



        /* ===== Bulk Action Bar ===== */
        .slist-bulk {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.65rem 1.25rem;
            background: var(--scal-danger-light); border-bottom: 1px solid var(--scal-danger-border);
        }
        .dark .slist-bulk { background: rgba(239,68,68,0.06); border-color: rgba(239,68,68,0.15); }

        .slist-bulk-text { font-size: 0.8rem; font-weight: 600; color: #991b1b; }
        .dark .slist-bulk-text { color: #fca5a5; }

        .slist-bulk-btn {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.35rem 0.75rem; border-radius: 0.375rem;
            border: 1px solid var(--scal-danger-border); background: white;
            cursor: pointer; color: var(--scal-danger); font-size: 0.75rem;
            font-weight: 600; transition: all 0.15s;
        }
        .slist-bulk-btn:hover { background: var(--scal-danger); color: white; border-color: var(--scal-danger); }
        .dark .slist-bulk-btn { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.25); color: #f87171; }
        .dark .slist-bulk-btn:hover { background: #dc2626; color: white; border-color: #dc2626; }

        .slist-bulk-clear {
            display: inline-flex; align-items: center; gap: 0.25rem;
            padding: 0.35rem 0.6rem; border-radius: 0.375rem;
            border: 1px solid #e5e7eb; background: white;
            cursor: pointer; color: #6b7280; font-size: 0.75rem;
            font-weight: 500; transition: all 0.15s; margin-left: auto;
        }
        .slist-bulk-clear:hover { background: #f3f4f6; color: #374151; }
        .dark .slist-bulk-clear { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); color: #9ca3af; }
        .dark .slist-bulk-clear:hover { background: rgba(255,255,255,0.1); color: #f3f4f6; }

        /* ===== List Section ===== */
        .slist {
            margin-top: 1.25rem; border-radius: 1rem; overflow: hidden;
            border: 1px solid #e5e7eb; background: white;
        }
        .dark .slist { border-color: rgba(255,255,255,0.06); background: rgba(255,255,255,0.02); }

        .slist-header {
            padding: 0.85rem 1.25rem; display: flex; align-items: center; gap: 0.5rem;
            border-bottom: 1px solid #f3f4f6;
        }
        .dark .slist-header { border-bottom-color: rgba(255,255,255,0.05); }

        .slist-title { font-weight: 600; font-size: 0.9rem; color: #111827; margin: 0; }
        .dark .slist-title { color: #f3f4f6; }

        .slist-count {
            margin-left: auto; background: var(--scal-primary-light);
            color: var(--scal-primary-dark); font-weight: 700; font-size: 0.7rem;
            padding: 0.15rem 0.55rem; border-radius: 9999px;
        }

        .slist-item {
            display: flex; align-items: center; padding: 0.7rem 1.25rem;
            border-bottom: 1px solid #f9fafb;
            transition: background 0.15s; background: white;
        }
        .slist-item:last-child { border-bottom: none; }
        .slist-item:hover { background: var(--scal-primary-subtle); }
        .slist-item--selected { background: #eef2ff !important; }
        .dark .slist-item { background: transparent; border-bottom-color: rgba(255,255,255,0.03); }
        .dark .slist-item:hover { background: var(--scal-primary-subtle); }
        .dark .slist-item--selected { background: rgba(99,102,241,0.08) !important; }

        /* ===== Checkbox ===== */
        .slist-checkbox, .slist-checkbox-all {
            width: 1.1rem; height: 1.1rem; border-radius: 0.25rem;
            border: 2px solid #d1d5db; cursor: pointer; flex-shrink: 0;
            margin-right: 0.75rem; appearance: none; -webkit-appearance: none;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.15s; position: relative; background: white;
        }
        .slist-checkbox-all { margin-right: 0.5rem; }
        .slist-checkbox:checked, .slist-checkbox-all:checked {
            background: var(--scal-accent); border-color: var(--scal-accent);
        }
        .slist-checkbox:checked::after, .slist-checkbox-all:checked::after {
            content: '✓'; color: white; font-size: 0.65rem; font-weight: 700; position: absolute;
        }
        .dark .slist-checkbox, .dark .slist-checkbox-all { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); }
        .dark .slist-checkbox:checked, .dark .slist-checkbox-all:checked { background: var(--scal-accent); border-color: var(--scal-accent); }

        .slist-icon {
            display: flex; align-items: center; justify-content: center;
            width: 2.25rem; height: 2.25rem; border-radius: 0.5rem;
            background: var(--scal-accent-light); border: 1px solid rgba(99,102,241,0.2);
            margin-right: 0.75rem; flex-shrink: 0;
        }
        .dark .slist-icon { border-color: rgba(129,140,248,0.2); }
        .slist-icon span { font-size: 0.7rem; font-weight: 700; color: var(--scal-accent); }
        .dark .slist-icon span { color: var(--scal-accent-dark); }

        .slist-name { font-weight: 600; font-size: 0.85rem; color: #111827; }
        .dark .slist-name { color: #f3f4f6; }

        .slist-meta { font-size: 0.7rem; color: #6b7280; margin-top: 0.1rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dark .slist-meta { color: #9ca3af; }

        .slist-date { font-size: 0.7rem; color: #9ca3af; white-space: nowrap; margin-left: 0.75rem; }
        .dark .slist-date { color: #6b7280; }

        .slist-agenda-badge {
            display: inline-block; padding: 0.1rem 0.4rem; border-radius: 9999px;
            background: var(--scal-accent); color: white;
            font-size: 0.65rem; font-weight: 600; margin-right: 0.35rem;
        }

        .slist-class-badge {
            display: inline-block; padding: 0.1rem 0.35rem; border-radius: 0.25rem;
            background: var(--scal-primary-light); color: var(--scal-primary-dark);
            font-size: 0.6rem; font-weight: 600; margin-right: 0.2rem; margin-top: 0.15rem;
        }

        .slist-actions {
            display: flex; align-items: center; gap: 0.35rem; margin-left: 0.75rem; flex-shrink: 0;
        }

        .slist-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 1.75rem; height: 1.75rem; border-radius: 0.375rem;
            border: 1px solid #e5e7eb; background: white; cursor: pointer;
            color: #6b7280; transition: all 0.15s; text-decoration: none;
        }
        .slist-btn:hover { background: #f3f4f6; color: #374151; }
        .dark .slist-btn { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); color: #9ca3af; }
        .dark .slist-btn:hover { background: rgba(255,255,255,0.1); color: #f3f4f6; }

        .slist-btn--danger:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
        .dark .slist-btn--danger:hover { background: rgba(239,68,68,0.1); color: #f87171; border-color: rgba(239,68,68,0.25); }

        /* ===== Empty State ===== */
        .scal-empty {
            margin-top: 1.25rem; border-radius: 1rem;
            border: 1px solid #e5e7eb; border-style: dashed;
            padding: 2.5rem 1.5rem; text-align: center; background: white;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }
        .dark .scal-empty { background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.08); }
        .scal-empty-icon { font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.6; }
        .scal-empty-title { font-weight: 600; color: #4b5563; margin: 0 0 0.25rem; font-size: 0.9rem; }
        .dark .scal-empty-title { color: #d1d5db; }
        .scal-empty-desc { font-size: 0.8rem; color: #9ca3af; margin: 0; }
        .dark .scal-empty-desc { color: #6b7280; }

        /* ===== Stats Bar ===== */
        .scal-stats {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;
            margin-bottom: 1rem;
        }
        @media (max-width: 640px) {
            .scal-stats { grid-template-columns: 1fr; }
        }
        .scal-stat {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.85rem 1rem; border-radius: 0.75rem;
            border: 1px solid #e5e7eb; background: white;
        }
        .dark .scal-stat { background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.06); }

        .scal-stat-icon {
            display: flex; align-items: center; justify-content: center;
            width: 2.5rem; height: 2.5rem; border-radius: 0.5rem;
            flex-shrink: 0;
        }
        .scal-stat-icon--schedule { background: var(--scal-accent-light); }
        .scal-stat-icon--schedule svg { color: var(--scal-accent); }
        .scal-stat-icon--class { background: var(--scal-primary-light); }
        .scal-stat-icon--class svg { color: var(--scal-primary); }
        .scal-stat-icon--holiday { background: var(--scal-danger-light); }
        .scal-stat-icon--holiday svg { color: var(--scal-danger); }

        .scal-stat-value { font-size: 1.25rem; font-weight: 700; color: #111827; line-height: 1; }
        .dark .scal-stat-value { color: #f3f4f6; }
        .scal-stat-label { font-size: 0.7rem; color: #6b7280; margin-top: 0.1rem; }
        .dark .scal-stat-label { color: #9ca3af; }
    </style>



    {{-- ═══════ Custom Confirmation Modal (Alpine.js) ═══════ --}}
    <div
        x-data="{
            show: false,
            title: '',
            message: '',
            action: null,
            open(title, message, action) {
                this.title = title;
                this.message = message;
                this.action = action;
                this.show = true;
            },
            confirm() {
                if (this.action) this.action();
                this.close();
            },
            close() {
                this.show = false;
                this.title = '';
                this.message = '';
                this.action = null;
            }
        }"
        x-on:confirm-delete.window="open(
            $event.detail.title ?? 'Hapus Data',
            $event.detail.message ?? 'Yakin ingin menghapus?',
            $event.detail.action
        )"
        x-cloak
    >
        <template x-if="show">
            <div class="confirm-overlay" x-on:click.self="close()" x-on:keydown.escape.window="close()">
                <div class="confirm-box">
                    <div class="confirm-icon-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <h3 class="confirm-title" x-text="title"></h3>
                    <p class="confirm-msg" x-text="message"></p>
                    <div class="confirm-actions">
                        <button type="button" class="confirm-btn confirm-btn--cancel" x-on:click="close()">Batal</button>
                        <button type="button" class="confirm-btn confirm-btn--danger" x-on:click="confirm()">Ya, Hapus</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div>
        {{-- Navigation --}}
        <div class="scal-nav">
            <button wire:click="previousMonth" type="button" class="scal-nav-btn">
                <x-heroicon-m-chevron-left class="w-4 h-4" />
            </button>
            <h2 class="scal-title">{{ $this->monthName }}</h2>
            <button wire:click="nextMonth" type="button" class="scal-nav-btn">
                <x-heroicon-m-chevron-right class="w-4 h-4" />
            </button>
        </div>

        {{-- Stats --}}
        @php
            $list = $this->schedulesList;
            $totalSchedules = $list->count();
            $totalClasses = $list->sum(fn($s) => count($s->classes));
            $uniqueAgendas = $list->pluck('agenda.name')->unique()->filter()->count();
        @endphp
        <div class="scal-stats">
            <div class="scal-stat">
                <div class="scal-stat-icon scal-stat-icon--schedule">
                    <x-heroicon-o-calendar-days class="w-5 h-5" />
                </div>
                <div>
                    <div class="scal-stat-value">{{ $totalSchedules }}</div>
                    <div class="scal-stat-label">Total Jadwal</div>
                </div>
            </div>
            <div class="scal-stat">
                <div class="scal-stat-icon scal-stat-icon--class">
                    <x-heroicon-o-academic-cap class="w-5 h-5" />
                </div>
                <div>
                    <div class="scal-stat-value">{{ $totalClasses }}</div>
                    <div class="scal-stat-label">Kelas Terjadwal</div>
                </div>
            </div>
            <div class="scal-stat">
                <div class="scal-stat-icon scal-stat-icon--holiday">
                    <x-heroicon-o-bookmark class="w-5 h-5" />
                </div>
                <div>
                    <div class="scal-stat-value">{{ $uniqueAgendas }}</div>
                    <div class="scal-stat-label">Agenda Aktif</div>
                </div>
            </div>
        </div>

        {{-- Calendar --}}
        <div class="scal-wrap">
            <div class="scal-header">
                @foreach (['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MIN'] as $d)
                    <div class="scal-header-cell">{{ $d }}</div>
                @endforeach
            </div>

            @foreach ($this->calendarData as $week)
                <div class="scal-week">
                    @foreach ($week as $cell)
                        @if ($cell === null)
                            <div class="scal-cell scal-cell--empty"></div>
                        @else
                            @php
                                $cls = 'scal-cell';
                                if ($cell['isToday']) $cls .= ' scal-cell--today';
                                if ($cell['isHoliday']) $cls .= ' scal-cell--holiday';
                            @endphp
                            <div wire:click="openDayModal('{{ $cell['date'] }}')" class="{{ $cls }}">
                                <div class="scal-day {{ $cell['isToday'] ? 'scal-day--today' : '' }}">
                                    {{ $cell['day'] }}
                                </div>

                                @if (count($cell['schedules']) > 0)
                                    <span class="scal-count-dot">{{ count($cell['schedules']) }}</span>
                                @endif

                                {{-- Holiday badges --}}
                                @foreach ($cell['holidays'] as $h)
                                    <span class="scal-badge scal-badge--holiday" title="{{ $h['name'] }}">{{ $h['name'] }}</span>
                                @endforeach

                                {{-- Schedule badges --}}
                                @php $scheduleSlice = array_slice($cell['schedules'], 0, 2); @endphp
                                @foreach ($scheduleSlice as $s)
                                    <span class="scal-badge scal-badge--agenda" title="{{ $s['agenda']['name'] ?? 'Jadwal' }}">
                                        {{ $s['agenda']['name'] ?? 'Jadwal' }}
                                    </span>
                                    @if (!empty($s['classes']))
                                        @php $classSlice = array_slice($s['classes'], 0, 2); @endphp
                                        @foreach ($classSlice as $c)
                                            <span class="scal-badge scal-badge--class" title="{{ $c['name'] }}">{{ $c['name'] }}</span>
                                        @endforeach
                                        @if (count($s['classes']) > 2)
                                            <span class="scal-badge scal-badge--more">+{{ count($s['classes']) - 2 }} kelas</span>
                                        @endif
                                    @endif
                                @endforeach

                                @if (count($cell['schedules']) > 2)
                                    <span class="scal-badge scal-badge--more">+{{ count($cell['schedules']) - 2 }} lainnya</span>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- List --}}
        @if ($list->isNotEmpty())
            <div class="slist">
                <div class="slist-header">
                    <input
                        type="checkbox"
                        class="slist-checkbox-all"
                        wire:click="toggleAllSchedules"
                        @checked(count($selectedScheduleIds) === $list->count() && $list->count() > 0)
                        title="Pilih Semua"
                    />
                    <h3 class="slist-title">Daftar Jadwal — {{ $this->monthName }}</h3>
                    <span class="slist-count">{{ $list->count() }} jadwal</span>
                </div>

                {{-- Bulk action bar --}}
                @if (count($selectedScheduleIds) > 0)
                    <div class="slist-bulk">
                        <span class="slist-bulk-text">{{ count($selectedScheduleIds) }} jadwal dipilih</span>
                        <button
                            type="button"
                            x-data
                            x-on:click="$dispatch('confirm-delete', {
                                title: 'Hapus {{ count($selectedScheduleIds) }} Jadwal',
                                message: 'Yakin ingin menghapus {{ count($selectedScheduleIds) }} jadwal yang dipilih? Tindakan ini tidak dapat dibatalkan.',
                                action: () => $wire.bulkDeleteSchedules()
                            })"
                            class="slist-bulk-btn">
                            <x-heroicon-o-trash class="w-3.5 h-3.5" />
                            Hapus Terpilih
                        </button>
                        <button
                            type="button"
                            wire:click="$set('selectedScheduleIds', [])"
                            class="slist-bulk-clear">
                            <x-heroicon-o-x-mark class="w-3 h-3" />
                            Batal
                        </button>
                    </div>
                @endif

                @foreach ($list as $schedule)
                    @php
                        $isSelected = in_array($schedule->id, $selectedScheduleIds);
                    @endphp
                    <div class="slist-item {{ $isSelected ? 'slist-item--selected' : '' }}">
                        <input
                            type="checkbox"
                            class="slist-checkbox"
                            wire:click="toggleSchedule({{ $schedule->id }})"
                            @checked($isSelected)
                        />
                        <div class="slist-icon">
                            <span>{{ \Carbon\Carbon::parse($schedule->date)->format('d') }}</span>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="slist-name">
                                <span class="slist-agenda-badge">{{ $schedule->agenda->name ?? '-' }}</span>
                            </div>
                            <div class="slist-meta">
                                @foreach ($schedule->classes as $c)
                                    <span class="slist-class-badge">{{ $c->name }}</span>
                                @endforeach
                            </div>
                            @if ($schedule->description)
                                <div class="slist-meta" style="margin-top:0.2rem; font-style: italic;">{{ Str::limit($schedule->description, 50) }}</div>
                            @endif
                        </div>
                        <div class="slist-date">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('l, d M Y') }}</div>
                        <div class="slist-actions">
                            <a href="{{ \App\Filament\Resources\Schedules\ScheduleResource::getUrl('view', ['record' => $schedule->id]) }}" class="slist-btn" title="Lihat Detail">
                                <x-heroicon-o-eye class="w-3.5 h-3.5" />
                            </a>
                            <a href="{{ \App\Filament\Resources\Schedules\ScheduleResource::getUrl('edit', ['record' => $schedule->id]) }}" class="slist-btn" title="Edit">
                                <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                            </a>
                            <button
                                type="button"
                                x-data
                                x-on:click="$dispatch('confirm-delete', {
                                    title: 'Hapus Jadwal',
                                    message: 'Yakin ingin menghapus jadwal \'{{ $schedule->agenda->name ?? '' }}\' pada {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d M Y') }}?',
                                    action: () => $wire.deleteSchedule({{ $schedule->id }})
                                })"
                                class="slist-btn slist-btn--danger"
                                title="Hapus">
                                <x-heroicon-o-trash class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="scal-empty">
                <div class="scal-empty-icon"><x-heroicon-s-calendar-days style="width: 3rem; height: 3rem; " /></div>
                <p class="scal-empty-title">Belum ada jadwal</p>
                <p class="scal-empty-desc">Belum ada jadwal yang terdaftar untuk bulan {{ $this->monthName }}.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
