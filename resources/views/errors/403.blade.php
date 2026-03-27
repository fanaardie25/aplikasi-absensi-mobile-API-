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
    <title>{{ $site_settings['site_name'] ?? '403 - Sistem Absensi SMKN Tengaran' }}</title>
    
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
        tailwind.config = { darkMode: 'class' }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        html.dark .glass-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(71, 85, 105, 0.5);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 antialiased min-h-screen flex flex-col justify-center relative overflow-hidden transition-colors duration-300">
    
    <div class="absolute top-0 left-0 w-full h-64 bg-emerald-600 dark:bg-emerald-800 z-0 transition-colors duration-300"></div>
    <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-emerald-600 dark:from-emerald-800 to-slate-50 dark:to-slate-900 z-0 transition-colors duration-300"></div>

    <main class="relative z-10 px-4 w-full flex justify-center">
        <div class="glass-card max-w-lg w-full p-10 md:p-14 text-center rounded-[2rem] shadow-2xl transition-all duration-300">
            <div class="text-9xl font-extrabold text-transparent bg-clip-text bg-gradient-to-br from-red-500 to-red-700 dark:from-red-400 dark:to-red-600 mb-6 drop-shadow-sm">
                403
            </div>
            
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white mb-4 transition-colors">Akses Tidak Diizinkan</h1>
            <p class="text-slate-500 dark:text-slate-400 text-base leading-relaxed mb-10 transition-colors">
                Anda tidak memiliki hak akses untuk membuka halaman ini. Silakan pastikan Anda telah masuk dengan akun yang memiliki otorisasi yang sesuai.
            </p>
            
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold rounded-xl hover:bg-emerald-600 dark:hover:bg-emerald-500 hover:text-white transition-all duration-300 shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </main>

    <div class="absolute bottom-6 w-full text-center z-10">
        <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest transition-colors">
            SMKN TENGARAN &copy; {{ now()->year }}
        </p>
    </div>

</body>
</html>