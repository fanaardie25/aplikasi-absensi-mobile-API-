<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Sistem Dalam Perawatan</title>
    
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
        
        /* Animasi putar lambat untuk ikon gear (opsional) */
        .spin-slow {
            animation: spin 4s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 antialiased min-h-screen flex flex-col justify-center relative overflow-hidden transition-colors duration-300">
    
    <div class="absolute top-0 left-0 w-full h-64 bg-emerald-600 dark:bg-emerald-800 z-0 transition-colors duration-300"></div>
    <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-emerald-600 dark:from-emerald-800 to-slate-50 dark:to-slate-900 z-0 transition-colors duration-300"></div>

    <main class="relative z-10 px-4 w-full flex justify-center">
        <div class="glass-card max-w-lg w-full p-10 md:p-14 text-center rounded-[2rem] shadow-2xl transition-all duration-300">
            
            <div class="flex justify-center mb-6 text-amber-500 dark:text-amber-400 drop-shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 spin-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white mb-4 transition-colors">Sistem Dalam Perawatan</h1>
            <p class="text-slate-500 dark:text-slate-400 text-base leading-relaxed mb-10 transition-colors">
                Mohon maaf atas ketidaknyamanannya. Sistem presensi saat ini sedang menjalani proses pembaruan dan perawatan rutin. Silakan coba kembali beberapa saat lagi.
            </p>
            
            <button onclick="window.location.reload()" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold rounded-xl hover:bg-emerald-600 dark:hover:bg-emerald-500 hover:text-white transition-all duration-300 shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-1 w-full sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Muat Ulang Halaman
            </button>
        </div>
    </main>

    <div class="absolute bottom-6 w-full text-center z-10">
        <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest transition-colors">
            SMKN TENGARAN &copy; {{ now()->year }}
        </p>
    </div>

</body>
</html>