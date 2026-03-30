<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if(isset($site_settings['site_favicon']))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $site_settings['site_favicon']) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    <title>{{ $site_settings['site_name'] ?? 'Sistem Absensi SMKN Tengaran' }}</title>
    
    <script>
        const theme = localStorage.getItem('theme');
        if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        
        /* Ubah media query menjadi class selector .dark */
        html.dark .glass-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(71, 85, 105, 0.5);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden transition-colors duration-300">
    
    <div class="absolute top-0 left-0 w-full h-64 bg-emerald-600 dark:bg-emerald-800 z-0 transition-colors duration-300"></div>
    <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-emerald-600 dark:from-emerald-800 to-slate-50 dark:to-slate-900 z-0 transition-colors duration-300"></div>

    <nav class="z-10 px-6 py-8 flex justify-center">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-xl shadow-lg flex items-center justify-center p-2 transition-colors duration-300">
                 <img src="{{ asset('logo.png') }}" alt="Logo" class="w-full h-auto">
            </div>
            <div class="text-white dark:text-slate-100">
                <p class="font-extrabold text-xl tracking-tight leading-none">SMKN TENGARAN</p>
                <p class="text-[10px] font-bold tracking-[0.2em] opacity-80 uppercase mt-1">Kabupaten Semarang</p>
            </div>
        </div>
    </nav>

    <main class="z-10 px-4 py-6 flex-grow flex flex-col items-center justify-center">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-4 transition-colors duration-300">
                Sistem Presensi <span class="text-emerald-600 dark:text-emerald-400 italic">Jumat.</span>
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-base md:text-lg max-w-xl mx-auto leading-relaxed transition-colors duration-300">
                Platform monitoring kehadiran siswa dalam kegiatan keagamaan untuk mewujudkan karakter religius dan disiplin.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 max-w-4xl w-full">
            
            <a href="{{ url('/admin/login') }}" class="group relative block">
                <div class="h-full p-8 rounded-3xl glass-card transition-all duration-300 hover:shadow-2xl hover:shadow-emerald-200/50 dark:hover:shadow-emerald-900/30 hover:-translate-y-1">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-6 group-hover:bg-emerald-600 dark:group-hover:bg-emerald-600 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-600 dark:text-slate-400 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-3 transition-colors duration-300">Administrator</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6 transition-colors duration-300">Kelola data master, konfigurasi jadwal, dan pantau seluruh laporan kehadiran secara terpusat.</p>
                    <div class="inline-flex items-center text-sm font-bold text-emerald-600 dark:text-emerald-400 group-hover:gap-2 transition-all">
                        Masuk Dashboard <span class="ml-1">→</span>
                    </div>
                </div>
            </a>

            <a href="{{ url('/teacher/login') }}" class="group relative block">
                <div class="h-full p-8 rounded-3xl glass-card transition-all duration-300 hover:shadow-2xl hover:shadow-emerald-200/50 dark:hover:shadow-emerald-900/30 hover:-translate-y-1">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-6 group-hover:bg-emerald-600 dark:group-hover:bg-emerald-600 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-600 dark:text-slate-400 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-3 transition-colors duration-300">Guru Pembimbing</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6 transition-colors duration-300">Monitoring kehadiran siswa, pantau grafik, dan kelola absensi harian.</p>
                    <div class="inline-flex items-center text-sm font-bold text-emerald-600 dark:text-emerald-400 group-hover:gap-2 transition-all">
                        Mulai Monitoring <span class="ml-1">→</span>
                    </div>
                </div>
            </a>

        </div>
    </main>

    <footer class="z-10 py-10 px-6 border-t border-slate-200 dark:border-slate-800 mt-12 bg-white dark:bg-slate-900 transition-colors duration-300">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-center md:text-left">
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1 transition-colors duration-300">Dikembangkan Oleh</p>
                <p class="text-sm font-extrabold text-slate-700 dark:text-slate-300 uppercase italic transition-colors duration-300">RPL <span class="text-emerald-600 dark:text-emerald-400">SMKN TENGARAN</span></p>
            </div>
            <div class="flex gap-6 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-tighter transition-colors duration-300">
                <span class="hover:text-emerald-600 dark:hover:text-emerald-400 cursor-help transition-colors">{{ now()->year }}</span>
                <span class="text-slate-200 dark:text-slate-700">|</span>
                <span class="text-slate-600 dark:text-slate-400 italic">v1.0</span>
            </div>
        </div>
    </footer>

</body>
</html>