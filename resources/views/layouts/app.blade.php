<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Koperasi Syariah BMI</title>
    <meta name="description" content="Sistem Informasi Evaluasi Pembinaan Usaha Anggota Koperasi Syariah Benteng Mikro Indonesia">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kopsyah-bmi-new.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full bg-gray-50 text-gray-900 antialiased" style="font-family:'Plus Jakarta Sans',sans-serif">

<div class="flex h-full" x-data="{
    sidebarOpen: false,
    openCategories: {
        operasional: {{ request()->routeIs('members.*', 'businesses.*', 'visits.*', 'evaluations.*', 'coaching.*') ? 'true' : 'false' }},
        pelaporan: {{ request()->routeIs('reports.*') ? 'true' : 'false' }},
        sistem: {{ request()->routeIs('users.*', 'branches.*', 'master.*', 'import.*') ? 'true' : 'false' }},
        keamanan: {{ request()->routeIs('audit.*') ? 'true' : 'false' }}
    }
}">

    {{-- ── Mobile overlay ──────────────────────────────────────────────────── --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-black/40 lg:hidden"></div>

    {{-- ── Sidebar ──────────────────────────────────────────────────────────── --}}
    <aside class="fixed inset-y-0 left-0 z-30 flex flex-col w-60 bg-[#009a4c] border-r border-[#00803f]
                  transform transition-transform duration-200 ease-in-out
                  lg:relative lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        {{-- Logo Brand --}}
        <a href="{{ route('home') }}" class="flex items-center justify-center px-4 py-4 border-b border-[#00803f] hover:bg-[#00803f]/50 transition-colors" title="Lihat Landing Page">
            <img src="{{ asset('images/logo-bmi-full.png') }}" alt="Logo BMI" class="h-9 max-w-[195px] w-auto object-contain">
        </a>

        {{-- User info --}}
        <div class="px-5 py-3 border-b border-[#00803f] flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
                <p class="text-emerald-100 text-[11px] truncate">{{ auth()->user()->roleLabel() }}</p>
            </div>
            <span class="w-2 h-2 shrink-0 bg-[#e4c85b] rounded-full" title="Online"></span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-3 space-y-1.5 overflow-y-auto">
            @php
                $userRole = auth()->user()->role;

                // Dashboard (Standalone top item)
                $isDashboardActive = request()->routeIs('dashboard');

                // Accordion Groups matching reference structure
                $accordionGroups = [
                    'operasional' => [
                        'label' => 'Operasional',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                        'routes' => ['members.*', 'businesses.*', 'visits.*', 'evaluations.*', 'coaching.*'],
                        'items' => [
                            [
                                'route' => 'members.index',
                                'label' => 'Anggota',
                                'roles' => ['petugas_lapangan','asisten_manajer','manajer','pengurus','pengawas','system_admin'],
                                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                            ],
                            [
                                'route' => 'businesses.index',
                                'label' => 'Usaha',
                                'roles' => ['petugas_lapangan','asisten_manajer','manajer','pengurus','pengawas','system_admin'],
                                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                            ],
                            [
                                'route' => 'visits.index',
                                'label' => 'Kunjungan',
                                'roles' => ['petugas_lapangan','asisten_manajer','manajer','pengurus','pengawas','system_admin'],
                                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                            ],
                            [
                                'route' => 'evaluations.index',
                                'label' => 'Evaluasi',
                                'roles' => ['petugas_lapangan','asisten_manajer','manajer','pengurus','pengawas','system_admin'],
                                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
                            ],
                            [
                                'route' => 'coaching.index',
                                'label' => 'Pembinaan',
                                'roles' => ['petugas_lapangan','asisten_manajer','manajer','pengurus','pengawas','system_admin'],
                                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                            ],
                        ],
                    ],
                    'pelaporan' => [
                        'label' => 'Pelaporan',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                        'routes' => ['reports.*'],
                        'items' => [
                            [
                                'route' => 'reports.index',
                                'label' => 'Laporan',
                                'roles' => ['asisten_manajer','manajer','pengurus','pengawas','system_admin'],
                                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                            ],
                            [
                                'route' => 'reports.analytics',
                                'label' => 'Analitik Cabang',
                                'roles' => ['asisten_manajer','manajer','pengurus','pengawas','system_admin'],
                                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                            ],
                        ],
                    ],
                    'sistem' => [
                        'label' => 'Sistem',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
                        'routes' => ['users.*', 'branches.*', 'master.*', 'import.*'],
                        'items' => [
                            [
                                'route' => 'users.index',
                                'label' => 'Pengguna',
                                'roles' => ['system_admin','manajer','pengurus'],
                                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
                            ],
                            [
                                'route' => 'branches.index',
                                'label' => 'Cabang',
                                'roles' => ['system_admin','pengurus','pengawas','manajer'],
                                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                            ],
                            [
                                'route' => 'master.index',
                                'label' => 'Master Data',
                                'roles' => ['system_admin','pengurus','manajer','pengawas'],
                                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>',
                            ],
                            [
                                'route' => 'import.index',
                                'label' => 'Impor Data',
                                'roles' => ['system_admin','manajer','pengurus'],
                                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>',
                            ],
                        ],
                    ],
                    'keamanan' => [
                        'label' => 'Keamanan',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                        'routes' => ['audit.*'],
                        'items' => [
                            [
                                'route' => 'audit.index',
                                'label' => 'Jejak Audit',
                                'roles' => ['manajer','pengurus','pengawas','system_admin'],
                                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                            ],
                        ],
                    ],
                ];
            @endphp

            {{-- Standalone Dashboard Item --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all
                      {{ $isDashboardActive
                          ? 'bg-white/20 text-white font-semibold shadow-sm'
                          : 'text-white/85 hover:text-white hover:bg-white/10' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span>Dashboard</span>
            </a>

            {{-- Accordion Categories --}}
            @foreach($accordionGroups as $groupKey => $group)
                @php
                    $visibleItems = array_filter($group['items'], fn($it) => in_array($userRole, $it['roles']));
                    $isGroupActive = request()->routeIs(...$group['routes']);
                @endphp
                @if(count($visibleItems) > 0)
                    <div class="space-y-1">
                        {{-- Category Toggle Button --}}
                        <button type="button"
                                @click="openCategories.{{ $groupKey }} = !openCategories.{{ $groupKey }}"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all cursor-pointer
                                       {{ $isGroupActive
                                           ? 'bg-white/15 text-white font-semibold'
                                           : 'text-white/85 hover:text-white hover:bg-white/10' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    {!! $group['icon'] !!}
                                </svg>
                                <span>{{ $group['label'] }}</span>
                            </div>
                            <svg class="w-3.5 h-3.5 shrink-0 transform transition-transform duration-200 text-white/70"
                                 :class="openCategories.{{ $groupKey }} ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Submenu Items (Indented with vertical connecting guide) --}}
                        <div x-show="openCategories.{{ $groupKey }}"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mt-1 ml-5 pl-3 border-l border-white/20 space-y-1">
                            @foreach($visibleItems as $item)
                                @php $isItemActive = request()->routeIs($item['route'] . '*'); @endphp
                                <a href="{{ route($item['route']) }}"
                                   class="flex items-center gap-2.5 px-3 py-1.5 text-xs rounded-lg transition-all
                                          {{ $isItemActive
                                              ? 'bg-white/20 text-white font-semibold shadow-sm'
                                              : 'text-white/75 hover:text-white hover:bg-white/10' }}">
                                    <svg class="w-3.5 h-3.5 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        {!! $item['icon'] !!}
                                    </svg>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>



        {{-- Logout --}}
        <div class="p-2 border-t border-[#00803f]">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-3 w-full px-3 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-[#00803f] transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main Content ────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top bar --}}
        <header class="flex items-center gap-4 px-5 py-3 bg-white border-b border-gray-200 shrink-0">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div>
                <h1 class="text-sm font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            </div>

            {{-- Action Center / Notification Badges --}}
            @php
                $waitingValidationCount = 0;
                $needsRevisionCount = 0;
                try {
                    if (auth()->user()->canValidate() || auth()->user()->hasAnyRole(['system_admin', 'pengurus'])) {
                        $waitingValidationCount = \App\Models\Evaluation::waitingValidation()->count();
                    }
                    if (auth()->user()->isOfficer()) {
                        $needsRevisionCount = \App\Models\Evaluation::where('status', \App\Enums\EvaluationStatus::NeedsRevision->value)->count();
                    }
                } catch (\Throwable $e) {}
            @endphp

            <div class="ml-auto flex items-center gap-3 text-xs text-gray-500">
                @if($waitingValidationCount > 0)
                    <a href="{{ route('evaluations.index', ['status' => 'waiting_validation']) }}"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 border border-amber-300 text-amber-800 font-semibold text-[11px] hover:bg-amber-100 transition-colors"
                       title="{{ $waitingValidationCount }} Evaluasi menunggu validasi">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>{{ $waitingValidationCount }} Perlu Validasi</span>
                    </a>
                @endif

                @if($needsRevisionCount > 0)
                    <a href="{{ route('evaluations.index', ['status' => 'needs_revision']) }}"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-orange-50 border border-orange-300 text-orange-800 font-semibold text-[11px] hover:bg-orange-100 transition-colors"
                       title="{{ $needsRevisionCount }} Evaluasi perlu direvisi">
                        <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                        <span>{{ $needsRevisionCount }} Perlu Revisi</span>
                    </a>
                @endif

                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 border border-[#e4c85b] bg-[#fefce8] text-[#9c7c10] font-medium text-[11px]">
                    <span class="w-1.5 h-1.5 bg-[#e4c85b]"></span>
                    BMI Syariah
                </span>
                <span class="hidden sm:inline text-gray-400 font-mono">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success') || session('error'))
            <div class="px-5 pt-4 shrink-0">
                @if(session('success'))
                    <div class="flex items-center gap-3 px-4 py-3 bg-[#e6f7ee] border border-[#009a4c] text-[#006331] text-sm">
                        <svg class="w-4 h-4 shrink-0 text-[#009a4c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-300 text-red-800 text-sm">
                        <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto px-5 pb-8">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
