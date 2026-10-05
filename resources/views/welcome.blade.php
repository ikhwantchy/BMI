<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koperasi Syariah BMI — Sistem Evaluasi Pembinaan Usaha</title>
    <meta name="description" content="Sistem Informasi Evaluasi dan Pembinaan Usaha Anggota Koperasi Syariah Benteng Mikro Indonesia">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kopsyah-bmi-new.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-white text-gray-900 antialiased" style="font-family:'Plus Jakarta Sans',sans-serif">

    {{-- Top Brand Accent Bar (BMI Colors: Hijau, Kuning, Biru) --}}
    <div class="h-1 flex w-full">
        <div class="h-full flex-1 bg-[#009a4c]"></div>
        <div class="h-full w-24 bg-[#e4c85b]"></div>
        <div class="h-full w-24 bg-[#00a1e8]"></div>
    </div>

    {{-- ── Top Navigation ────────────────────────────────────────────────────── --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo with text on Landing Page --}}
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="flex items-center">
                        <img src="{{ asset('images/logo-kopsyah-bmi-new-text.png') }}"
                             alt="Koperasi Syariah BMI"
                             class="h-9 w-auto object-contain">
                    </a>
                </div>

                {{-- Nav Links --}}
                <nav class="hidden md:flex items-center space-x-8 text-xs font-semibold uppercase tracking-wider text-gray-600">
                    <a href="#fitur" class="hover:text-[#009a4c] transition-colors">Fitur Sistem</a>
                    <a href="#alur" class="hover:text-[#009a4c] transition-colors">Alur Evaluasi</a>
                    <a href="#tentang" class="hover:text-[#009a4c] transition-colors">Tentang BMI</a>
                </nav>

                {{-- CTA Button --}}
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('members.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-[#009a4c] hover:bg-[#007d3e] text-white text-xs font-semibold uppercase tracking-wider transition-colors">
                            <span>Buka Sistem</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-[#009a4c] hover:bg-[#007d3e] text-white text-xs font-semibold uppercase tracking-wider transition-colors">
                            <span>Masuk Sistem</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- ── Hero Section (Pure White Minimalist) ───────────────────────────────── --}}
    <section class="bg-white py-16 sm:py-24 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-3 py-1 border border-gray-200 bg-white text-xs font-semibold tracking-wider text-gray-700 uppercase mb-8">
                <span class="w-2 h-2 bg-[#009a4c]"></span>
                <span>Koperasi Syariah Benteng Mikro Indonesia</span>
            </div>

            {{-- Main Title --}}
            <h1 class="text-3xl sm:text-5xl font-semibold text-gray-900 tracking-tight max-w-4xl mx-auto leading-tight sm:leading-none">
                Sistem Evaluasi &amp; Pembinaan Usaha Anggota
            </h1>

            {{-- Subtitle --}}
            <p class="mt-6 text-sm sm:text-base text-gray-600 max-w-2xl mx-auto leading-relaxed font-normal">
                Platform terpadu untuk monitoring lapangan, standarisasi penilaian kelayakan usaha berbasis syariah, validasi berjenjang manajerial, dan tindak lanjut pembinaan anggota.
            </p>

            {{-- Action Buttons --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('login') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#009a4c] hover:bg-[#007d3e] text-white text-sm font-semibold uppercase tracking-wider transition-colors">
                    <span>Masuk ke Akun</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
                <a href="#fitur"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold uppercase tracking-wider transition-colors">
                    <span>Pelajari Fitur</span>
                </a>
            </div>

            {{-- 3 Value Pillars (Featuring Brand Colors on Sharp Minimalist Borders) --}}
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                {{-- Pillar 1 (Green) --}}
                <div class="bg-white border border-gray-200 border-t-4 border-t-[#009a4c] p-6">
                    <div class="text-xs font-semibold text-[#009a4c] uppercase tracking-wider mb-2">01 / Standarisasi</div>
                    <h2 class="text-base font-semibold text-gray-900 mb-2">5 Parameter Usaha Terukur</h2>
                    <p class="text-xs text-gray-600 leading-relaxed font-normal">
                        Kondisi keuangan, manajemen operasional, kepatuhan prinsip syariah, legalitas usaha, dan potensi pasar dinilai secara objektif dengan bobot terkalibrasi.
                    </p>
                </div>

                {{-- Pillar 2 (Yellow) --}}
                <div class="bg-white border border-gray-200 border-t-4 border-t-[#e4c85b] p-6">
                    <div class="text-xs font-semibold text-[#9c7c10] uppercase tracking-wider mb-2">02 / Akuntabilitas</div>
                    <h2 class="text-base font-semibold text-gray-900 mb-2">Monitoring Kunjungan Lapangan</h2>
                    <p class="text-xs text-gray-600 leading-relaxed font-normal">
                        Pencatatan tanggal kunjungan berkala, dokumentasi foto fisik lokasi usaha anggota, dan catatan verifikasi aktual di lapangan oleh petugas.
                    </p>
                </div>

                {{-- Pillar 3 (Blue) --}}
                <div class="bg-white border border-gray-200 border-t-4 border-t-[#00a1e8] p-6">
                    <div class="text-xs font-semibold text-[#0084be] uppercase tracking-wider mb-2">03 / Pembinaan</div>
                    <h2 class="text-base font-semibold text-gray-900 mb-2">Rekomendasi &amp; Tindak Lanjut</h2>
                    <p class="text-xs text-gray-600 leading-relaxed font-normal">
                        Sistem otomatis menghasilkan status kelayakan usaha (Lanjutkan, Pembinaan Khusus, Evaluasi Ulang) disertai pelacakan kemajuan terarah.
                    </p>
                </div>
            </div>

        </div>
    </section>

    {{-- ── Features Section (Fitur Utama) ─────────────────────────────────────── --}}
    <section id="fitur" class="bg-white py-16 sm:py-20 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 pb-4 border-b border-gray-100 gap-4">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-[#009a4c]">Modul Terintegrasi</span>
                    <h2 class="text-2xl font-semibold text-gray-900 tracking-tight mt-1">Kapabilitas Sistem BMI</h2>
                </div>
                <p class="text-xs text-gray-500 max-w-md font-normal">
                    Dirancang untuk Petugas Lapangan, Asisten Manajer, dan Manajer Cabang Koperasi BMI dalam satu alur kerja transparan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                {{-- Feature 1 --}}
                <div class="bg-white border border-gray-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 border border-gray-200 flex items-center justify-center text-gray-700 mb-4 font-mono text-xs font-semibold">
                            01
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Basis Data Anggota &amp; Usaha</h3>
                        <p class="text-xs text-gray-600 leading-relaxed font-normal">
                            Pendataan anggota terstruktur lengkap dengan sektor industri, omzet bulanan, riwayat evaluasi, dan rekam jejak pembinaan.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100 text-[11px] font-semibold text-[#009a4c] uppercase tracking-wider">
                        Data Terpusat
                    </div>
                </div>

                {{-- Feature 2 --}}
                <div class="bg-white border border-gray-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 border border-gray-200 flex items-center justify-center text-gray-700 mb-4 font-mono text-xs font-semibold">
                            02
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Kunjungan &amp; Bukti Visual</h3>
                        <p class="text-xs text-gray-600 leading-relaxed font-normal">
                            Jadwal visitasi berkala dilengkapi formulir unggah bukti dokumentasi foto usaha langsung dari lapangan oleh petugas.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100 text-[11px] font-semibold text-[#009a4c] uppercase tracking-wider">
                        Verifikasi Faktual
                    </div>
                </div>

                {{-- Feature 3 --}}
                <div class="bg-white border border-gray-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 border border-gray-200 flex items-center justify-center text-gray-700 mb-4 font-mono text-xs font-semibold">
                            03
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Kalkulator Skor Interaktif</h3>
                        <p class="text-xs text-gray-600 leading-relaxed font-normal">
                            Penghitungan skor akhir instan dengan formula pembobotan matematis dan pengelompokan tingkat kelayakan otomatis.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100 text-[11px] font-semibold text-[#009a4c] uppercase tracking-wider">
                        Skor Otomatis
                    </div>
                </div>

                {{-- Feature 4 --}}
                <div class="bg-white border border-gray-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 border border-gray-200 flex items-center justify-center text-gray-700 mb-4 font-mono text-xs font-semibold">
                            04
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Validasi Manajerial &amp; Ekspor</h3>
                        <p class="text-xs text-gray-600 leading-relaxed font-normal">
                            Persetujuan berjenjang oleh Manajer Cabang, pelaporan komprehensif, dan ekspor lembar audit dalam format CSV dan cetak.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100 text-[11px] font-semibold text-[#009a4c] uppercase tracking-wider">
                        Kontrol Audit
                    </div>
                </div>

            </div>

        </div>
    </section>

    {{-- ── Workflow Section (Alur Kerja) ──────────────────────────────────────── --}}
    <section id="alur" class="bg-white py-16 sm:py-20 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#009a4c]">Tahapan Operasional</span>
                <h2 class="text-2xl font-semibold text-gray-900 tracking-tight mt-1">Alur Evaluasi &amp; Pembinaan</h2>
                <p class="text-xs text-gray-500 mt-2 font-normal">
                    Proses 4 langkah terstruktur untuk memastikan tata kelola usaha anggota tetap sesuai koridor kepatuhan syariah dan keberlanjutan ekonomi.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white border border-gray-200 p-6 relative">
                    <span class="text-xs font-mono text-gray-400 font-semibold">LANGKAH 01</span>
                    <h3 class="text-sm font-semibold text-gray-900 mt-2 mb-2">Agenda Kunjungan</h3>
                    <p class="text-xs text-gray-600 leading-relaxed font-normal">
                        Petugas menjadwalkan kunjungan, meninjau lokasi usaha anggota, dan mencatat fakta temuan lapangan serta foto dokumentasi.
                    </p>
                </div>

                <div class="bg-white border border-gray-200 p-6 relative">
                    <span class="text-xs font-mono text-gray-400 font-semibold">LANGKAH 02</span>
                    <h3 class="text-sm font-semibold text-gray-900 mt-2 mb-2">Penilaian 5 Parameter</h3>
                    <p class="text-xs text-gray-600 leading-relaxed font-normal">
                        Petugas menginput skor tiap indikator secara objektif, menelaah bobot, dan merumuskan draf rekomendasi awal.
                    </p>
                </div>

                <div class="bg-white border border-gray-200 p-6 relative">
                    <span class="text-xs font-mono text-gray-400 font-semibold">LANGKAH 03</span>
                    <h3 class="text-sm font-semibold text-gray-900 mt-2 mb-2">Validasi Manajer</h3>
                    <p class="text-xs text-gray-600 leading-relaxed font-normal">
                        Manajer Cabang memeriksa dokumen bukti, menyetujui evaluasi atau memberikan catatan revisi jika data belum memadai.
                    </p>
                </div>

                <div class="bg-white border border-gray-200 p-6 relative">
                    <span class="text-xs font-mono text-gray-400 font-semibold">LANGKAH 04</span>
                    <h3 class="text-sm font-semibold text-gray-900 mt-2 mb-2">Tindak Lanjut Pembinaan</h3>
                    <p class="text-xs text-gray-600 leading-relaxed font-normal">
                        Rencana aksi pembinaan dieksekusi dengan tanggal target, pendampingan berkala, dan dokumentasi kemajuan usaha.
                    </p>
                </div>

            </div>

        </div>
    </section>

    {{-- ── About / Koperasi BMI Profile ──────────────────────────────────────── --}}
    <section id="tentang" class="bg-white py-16 sm:py-20 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 p-8 sm:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-4 text-center lg:text-left">
                        <img src="{{ asset('images/logo-kopsyah-bmi-new-text.png') }}"
                             alt="Logo Koperasi Syariah BMI"
                             class="h-12 w-auto mx-auto lg:mx-0 object-contain mb-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Koperasi Syariah Benteng Mikro Indonesia
                        </p>
                    </div>
                    <div class="lg:col-span-8 border-t lg:border-t-0 lg:border-l border-gray-200 pt-6 lg:pt-0 lg:pl-8">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">
                            Mewujudkan Kemandirian Ekonomi Umat Berlandaskan Syariah
                        </h2>
                        <p class="text-xs text-gray-600 leading-relaxed font-normal mb-4">
                            Koperasi Syariah BMI senantiasa mendampingi anggota sektor mikro melalui pembiayaan syariah yang berkeadilan, edukasi tata kelola keuangan, dan pembinaan berkala demi meningkatkan taraf hidup serta ketahanan ekonomi keluarga anggota.
                        </p>
                        <div class="flex flex-wrap gap-4 text-xs font-semibold text-gray-700">
                            <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-[#009a4c]"></span> Nilai Syariah</span>
                            <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-[#e4c85b]"></span> Pemberdayaan Mikro</span>
                            <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-[#00a1e8]"></span> Transparansi &amp; Amanah</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Minimalist Clean Footer ────────────────────────────────────────────── --}}
    <footer class="bg-white py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-kopsyah-bmi-new.png') }}" alt="Logo Icon" class="h-6 w-auto object-contain">
                    <span class="font-medium text-gray-700">Sistem Evaluasi Pembinaan Usaha &bull; Kopsyah BMI</span>
                </div>
                <div class="font-mono text-[11px] text-gray-400">
                    &copy; {{ date('Y') }} Koperasi Syariah Benteng Mikro Indonesia. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
