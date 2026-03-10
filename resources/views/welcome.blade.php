<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Sistem Presensi Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-white antialiased min-h-screen flex flex-col justify-center items-center p-6 relative overflow-hidden">
    
    <div class="absolute top-0 -left-20 w-72 h-72 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute bottom-0 -right-20 w-72 h-72 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>

    <div class="z-10 text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-emerald-400">
            Sistem Informasi Presensi
        </h1>
        <p class="text-slate-400 text-lg">Silakan pilih akses masuk untuk melanjutkan ke dashboard.</p>
    </div>

<div class="grid md:grid-cols-2 gap-8 max-w-4xl w-full z-10 px-4">
        
        <a href="{{ url('/admin/login') }}" class="group relative p-8 rounded-3xl bg-white/5 border border-white/10 hover:border-blue-500/50 transition-all duration-300 hover:bg-white/10 hover:-translate-y-1">
            <div class="mb-6 inline-flex p-4 rounded-2xl bg-blue-500/20 text-blue-400 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold mb-2">Administrator</h2>
            <p class="text-slate-400 leading-relaxed">Kelola Master Data, konfigurasi Tahun Ajaran, dan hak akses pengguna sistem.</p>
            <div class="mt-8 flex items-center text-blue-400 font-semibold">
                Masuk Sistem <span class="ml-2 group-hover:translate-x-2 transition-transform duration-300">→</span>
            </div>
        </a>

        <a href="{{ url('/teacher/login') }}" class="group relative p-8 rounded-3xl bg-white/5 border border-white/10 hover:border-emerald-500/50 transition-all duration-300 hover:bg-white/10 hover:-translate-y-1">
            <div class="mb-6 inline-flex p-4 rounded-2xl bg-emerald-500/20 text-emerald-400 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold mb-2">Guru Pembimbing</h2>
            <p class="text-slate-400 leading-relaxed">Pantau presensi harian siswa binaan, rekap kehadiran kelas, dan jadwal KBM.</p>
            <div class="mt-8 flex items-center text-emerald-400 font-semibold">
                Mulai Monitoring <span class="ml-2 group-hover:translate-x-2 transition-transform duration-300">→</span>
            </div>
        </a>

    </div>

    <footer class="mt-16 text-slate-500 text-sm italic">
        © 2026 SMKN Tengaran.
    </footer>

</body>
</html>