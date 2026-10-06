<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesi Kedaluwarsa - Koperasi Syariah BMI</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-800 flex flex-col min-h-screen">
    {{-- Brand color top stripe --}}
    <div class="h-1 flex w-full fixed top-0 left-0 z-50">
        <div class="h-full flex-1 bg-[#009a4c]"></div>
        <div class="h-full w-24 bg-[#e4c85b]"></div>
        <div class="h-full w-24 bg-[#00a1e8]"></div>
    </div>

    <div class="flex-1 flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white border border-gray-200 p-8 text-center shadow-sm">
            <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-200">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <span class="inline-block px-2.5 py-0.5 text-xs font-semibold bg-amber-100 text-amber-800 rounded-full mb-3">
                Error 419 • Sesi Kedaluwarsa
            </span>

            <h1 class="text-lg font-bold text-gray-900 mb-2">Halaman Tidak Aktif Terlalu Lama</h1>
            <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                Demi keamanan akun Anda, sesi halaman telah berakhir. Silakan muat ulang halaman untuk memperbarui sesi login.
            </p>

            <div class="space-y-3">
                <button onclick="window.location.reload()"
                        class="w-full py-2.5 px-4 bg-[#009a4c] hover:bg-[#007d3e] text-white text-xs font-semibold uppercase tracking-wider transition-colors cursor-pointer block">
                    Muat Ulang Halaman
                </button>
                <a href="{{ route('login') }}"
                   class="w-full py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold uppercase tracking-wider transition-colors block">
                    Kembali ke Halaman Login
                </a>
            </div>
        </div>
    </div>

    <footer class="text-center text-xs text-gray-400 pb-6">
        &copy; {{ date('Y') }} Koperasi Syariah BMI &bull; Melayani dengan Hati Nurani
    </footer>
</body>
</html>
