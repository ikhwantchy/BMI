<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Evaluasi Usaha') — Koperasi Syariah BMI</title>
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
    sidebarOpen: true,
    profileModal: false,
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
    <aside x-show="sidebarOpen"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 bg-[#008f45] border-r border-[#007437] shadow-lg lg:relative shrink-0">

        {{-- Logo Brand --}}
        <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 transition-opacity hover:opacity-90" title="Koperasi Syariah BMI">
                <img src="{{ asset('images/logo-bmi-full.png') }}" alt="Logo Koperasi Syariah BMI" class="h-9 w-auto object-contain">
            </a>
            <button @click="sidebarOpen = false"
                    class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors"
                    title="Tutup sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-3 space-y-1.5 overflow-y-auto">
            @php
                $userRole = auth()->user()->role;

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

            {{-- Accordion Categories --}}
            @foreach($accordionGroups as $groupKey => $group)
                @php
                    $visibleItems = array_filter($group['items'], fn($it) => in_array($userRole, $it['roles']));
                    $isGroupActive = request()->routeIs(...$group['routes']);
                @endphp
                @if(count($visibleItems) > 0)
                    <div class="space-y-1">
                        {{-- Category Toggle Button (No icon on category header, clean text + chevron) --}}
                        <button type="button"
                                @click="openCategories.{{ $groupKey }} = !openCategories.{{ $groupKey }}"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition-all cursor-pointer text-white
                                       {{ $isGroupActive ? 'bg-white/20 shadow-xs' : 'hover:bg-white/10' }}">
                            <span class="text-white text-xs font-semibold tracking-wide">{{ $group['label'] }}</span>
                            <svg class="w-3.5 h-3.5 shrink-0 transform transition-transform duration-200 text-white/80"
                                 :class="openCategories.{{ $groupKey }} ? 'rotate-90' : ''"
                                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        {{-- Submenu Items (With icon and indented guide line) --}}
                        <div x-show="openCategories.{{ $groupKey }}"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mt-1 ml-4 pl-3.5 border-l-2 border-white/20 space-y-1">
                            @foreach($visibleItems as $item)
                                @php $isItemActive = request()->routeIs($item['route'] . '*'); @endphp
                                <a href="{{ route($item['route']) }}"
                                   class="flex items-center gap-2.5 px-3 py-2 text-xs rounded-lg transition-all text-white
                                          {{ $isItemActive
                                              ? 'bg-white/25 font-bold shadow-xs'
                                              : 'hover:bg-white/10 text-white/90' }}">
                                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        {!! $item['icon'] !!}
                                    </svg>
                                    <span class="text-white text-xs {{ $isItemActive ? 'font-bold' : 'font-medium' }}">{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>

        {{-- Collapse Sidebar (Full Screen Mode) --}}
        <div class="p-3 border-t border-white/10">
            <button type="button"
                    @click="sidebarOpen = false"
                    class="flex items-center justify-between w-full px-3.5 py-2.5 text-xs font-semibold text-white/90 hover:text-white hover:bg-white/10 rounded-xl transition-all cursor-pointer"
                    title="Tutup sidebar untuk mode layar penuh">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                    <span>Layar Penuh</span>
                </div>
                <span class="text-[10px] font-mono px-2 py-0.5 bg-black/20 rounded-md text-emerald-200 font-bold">Tutup</span>
            </button>
        </div>
    </aside>

    {{-- ── Main Content ────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top bar --}}
        <header class="flex items-center justify-between px-6 py-3.5 bg-white border-b border-gray-200 shrink-0 shadow-xs">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer"
                        :title="sidebarOpen ? 'Tutup sidebar (Layar Penuh)' : 'Buka sidebar'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-base font-bold text-gray-800 tracking-tight">@yield('page-title', 'Sistem Evaluasi Usaha')</h1>
                </div>
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

            <div class="flex items-center gap-3 text-xs">
                @if($waitingValidationCount > 0)
                    <a href="{{ route('evaluations.index', ['status' => 'waiting_validation']) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-300 text-amber-800 font-semibold text-xs rounded-full hover:bg-amber-100 transition-colors"
                       title="{{ $waitingValidationCount }} Evaluasi menunggu validasi">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>{{ $waitingValidationCount }} Perlu Validasi</span>
                    </a>
                @endif

                @if($needsRevisionCount > 0)
                    <a href="{{ route('evaluations.index', ['status' => 'needs_revision']) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 border border-orange-300 text-orange-800 font-semibold text-xs rounded-full hover:bg-orange-100 transition-colors"
                       title="{{ $needsRevisionCount }} Evaluasi perlu direvisi">
                        <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                        <span>{{ $needsRevisionCount }} Perlu Revisi</span>
                    </a>
                @endif

                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full border border-[#e4c85b]/60 bg-[#fefce8] text-[#9c7c10] font-semibold text-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#e4c85b]"></span>
                    BMI Syariah
                </span>

                {{-- User Profile Pill & Dropdown (Top Right) --}}
                <div class="relative ml-2" x-data="{ profileDropdown: false }" @click.outside="profileDropdown = false">
                    <button type="button"
                            @click="profileDropdown = !profileDropdown"
                            class="flex items-center gap-2.5 p-1 pl-3 rounded-full hover:bg-gray-100 border border-gray-200 transition-colors cursor-pointer">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-bold text-gray-900 leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-gray-500 font-semibold leading-tight mt-0.5">{{ auth()->user()->roleLabel() }}</p>
                        </div>
                        {{-- Profile Icon Button matching reference --}}
                        <div class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center shrink-0 transition-colors">
                            <svg class="w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </button>

                    {{-- Dropdown Menu matching user image 3 --}}
                    <div x-show="profileDropdown"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-64 rounded-2xl bg-white shadow-xl ring-1 ring-black/5 z-50 divide-y divide-gray-100 overflow-hidden"
                         style="display: none;">
                        {{-- User Header with Email & Role --}}
                        <div class="px-4 py-3 bg-gray-50/70">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
                            <span class="inline-block mt-2 px-2.5 py-0.5 text-[10px] font-bold uppercase font-mono tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-full">
                                {{ auth()->user()->roleLabel() }}
                            </span>
                        </div>

                        {{-- Action: Lihat Profile --}}
                        <div class="py-1">
                            <button type="button"
                                    @click="profileDropdown = false; profileModal = true"
                                    class="flex items-center gap-3 w-full px-4 py-2.5 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors cursor-pointer">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Lihat Profil</span>
                            </button>
                        </div>

                        {{-- Action: Logout --}}
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center gap-3 w-full px-4 py-2.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>Keluar (Log out)</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success') || session('error'))
            <div class="px-6 pt-4 shrink-0">
                @if(session('success'))
                    <div class="flex items-center gap-3 px-4 py-3 bg-[#e6f7ee] border border-[#009a4c] text-[#006331] text-sm rounded-xl shadow-xs">
                        <svg class="w-5 h-5 shrink-0 text-[#009a4c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-300 text-red-800 text-sm rounded-xl shadow-xs">
                        <svg class="w-5 h-5 shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
            </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto bg-gray-50 px-6 py-6">
            @yield('content')
        </main>
    </div>

    {{-- Modal Lihat Profile --}}
    <div x-show="profileModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-xs" @click="profileModal = false"></div>
            <div class="relative w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border border-gray-100 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#009a4c] flex items-center justify-center font-bold text-base border border-emerald-200/60">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Profil Pengguna</h3>
                            <p class="text-[11px] text-gray-500">Informasi akun Anda di Kopsyah BMI</p>
                        </div>
                    </div>
                    <button @click="profileModal = false" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100 transition-colors">
                        ✕
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="bg-gray-50 rounded-2xl p-4 space-y-2.5 border border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Nama Lengkap</span>
                            <span class="font-bold text-gray-900">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Username</span>
                            <span class="font-mono text-gray-700 font-semibold">{{ '@' . auth()->user()->username }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Email</span>
                            <span class="font-mono text-gray-700">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Peran / Role</span>
                            <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase font-mono tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-full">
                                {{ auth()->user()->roleLabel() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Cabang Tugas</span>
                            <span class="font-medium text-gray-800">{{ auth()->user()->branch?->name ?? 'Pusat / Lintas Cabang' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Status Akun</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="button" @click="profileModal = false"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>
