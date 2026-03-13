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
    <title>{{ $site_settings['site_name'] ?? 'Sistem Absensi' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#0f1115] text-white antialiased min-h-screen flex flex-col justify-center items-center p-6 relative">
    
    <div class="absolute inset-0 z-0 opacity-20" style="background-image: radial-gradient(#2f333c 1px, transparent 1px); background-size: 30px 30px;"></div>

    <div class="z-10 text-center mb-16 px-4">
        <span class="px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-[10px] font-bold tracking-[0.3em] uppercase text-emerald-500 mb-6 inline-block">Official Portal Presensi Sholat Jumat</span>
        <h1 class="text-5xl md:text-6xl font-black tracking-tighter mb-4 uppercase">
            SMKN <span class="text-emerald-500">Tengaran</span>
        </h1>
        <p class="text-slate-500 text-lg max-w-md mx-auto font-medium">Monitoring kehadiran siswa secara real-time dan terintegrasi.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6 max-w-5xl w-full z-10 px-4">
        
        <a href="{{ url('/admin/login') }}" class="group relative overflow-hidden p-10 rounded-3xl bg-[#16181d] border border-white/5 transition-all duration-500 hover:border-blue-500/30">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/10 transition-colors"></div>
            
            <div class="relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center mb-8 border border-white/10 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 group-hover:rotate-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold mb-3 tracking-tight">Administrator</h2>
                <p class="text-slate-500 text-sm leading-relaxed">Kelola Master Data, konfigurasi Tahun Ajaran, dan hak akses pengguna sistem.</p>
                <div class="mt-10 flex items-center text-[10px] font-black uppercase tracking-[0.2em] text-blue-400">
                    Masuk Sistem <span class="ml-2 group-hover:translate-x-2 transition-transform duration-300">→</span>
                </div>
            </div>
        </a>

        <a href="{{ url('/teacher/login') }}" class="group relative overflow-hidden p-10 rounded-3xl bg-[#16181d] border border-white/5 transition-all duration-500 hover:border-emerald-500/30">
             <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-colors"></div>

            <div class="relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center mb-8 border border-white/10 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500 group-hover:-rotate-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold mb-3 tracking-tight">Guru Pembimbing</h2>
                <p class="text-slate-500 text-sm leading-relaxed">Pantau presensi harian siswa binaan dan rekap kehadiran kelas.</p>
                <div class="mt-10 flex items-center text-[10px] font-black uppercase tracking-[0.2em] text-emerald-400">
                    Mulai Monitoring <span class="ml-2 group-hover:translate-x-2 transition-transform duration-300">→</span>
                </div>
            </div>
        </a>

    </div>

    <footer class="mt-20 z-10 py-8 border-t border-white/5 w-full max-w-5xl flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] uppercase tracking-[0.3em] text-slate-600 font-bold">
        <span>© 2026 SMKN Tengaran</span>
        <div class="flex gap-8">
            <span class="text-slate-800">Internal System</span>
            <span>Secure Access</span>
        </div>
    </footer>

</body>
</html>