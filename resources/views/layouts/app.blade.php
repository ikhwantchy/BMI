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
    <aside :class="sidebarOpen ? 'w-60 translate-x-0' : 'w-14 -translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-30 flex flex-col bg-[#008f45] border-r border-[#007437] shadow-lg lg:relative shrink-0 transition-all duration-200 ease-in-out">

        {{-- Logo Brand --}}
        <div class="h-14 border-b border-white/10 flex items-center shrink-0 transition-all duration-200"
             :class="sidebarOpen ? 'px-4 justify-start' : 'px-0 justify-center'">
            <a href="{{ route('home') }}" class="flex items-center transition-opacity hover:opacity-90" title="Koperasi Syariah BMI">
                <img x-show="sidebarOpen"
                     src="{{ asset('images/logo-bmi-full.png') }}"
                     alt="Logo Koperasi Syariah BMI"
                     class="h-8 w-auto object-contain">
                <img x-show="!sidebarOpen"
                     src="{{ asset('images/logo-kopsyah-bmi-new.png') }}"
                     alt="Logo Koperasi Syariah BMI"
                     class="h-7 w-7 object-contain mx-auto"
                     style="display: none;">
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto overflow-x-hidden transition-all duration-200"
             :class="sidebarOpen ? 'px-2 py-2.5 space-y-1' : 'px-1.5 py-2 space-y-1.5'">
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
                            ],
                            [
                                'route' => 'businesses.index',
                                'label' => 'Usaha',
                                'roles' => ['petugas_lapangan','asisten_manajer','manajer','pengurus','pengawas','system_admin'],
                            ],
                            [
                                'route' => 'visits.index',
                                'label' => 'Kunjungan',
                                'roles' => ['petugas_lapangan','asisten_manajer','manajer','pengurus','pengawas','system_admin'],
                            ],
                            [
                                'route' => 'evaluations.index',
                                'label' => 'Evaluasi',
                                'roles' => ['petugas_lapangan','asisten_manajer','manajer','pengurus','pengawas','system_admin'],
                            ],
                            [
                                'route' => 'coaching.index',
                                'label' => 'Pembinaan',
                                'roles' => ['petugas_lapangan','asisten_manajer','manajer','pengurus','pengawas','system_admin'],
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
                            ],
                            [
                                'route' => 'reports.analytics',
                                'label' => 'Analitik Cabang',
                                'roles' => ['asisten_manajer','manajer','pengurus','pengawas','system_admin'],
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
                            ],
                            [
                                'route' => 'branches.index',
                                'label' => 'Cabang',
                                'roles' => ['system_admin','pengurus','pengawas','manajer'],
                            ],
                            [
                                'route' => 'master.index',
                                'label' => 'Master Data',
                                'roles' => ['system_admin','pengurus','manajer','pengawas'],
                            ],
                            [
                                'route' => 'import.index',
                                'label' => 'Impor Data',
                                'roles' => ['system_admin','manajer','pengurus'],
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
                    <div>
                        {{-- Expanded Mode (Full Sidebar) --}}
                        <div x-show="sidebarOpen" class="space-y-1">
                            {{-- Category Toggle Button (Icon + Label + Chevron) --}}
                            <button type="button"
                                    @click="openCategories.{{ $groupKey }} = !openCategories.{{ $groupKey }}"
                                    class="w-full flex items-center justify-between px-2.5 py-2 rounded-md text-[13px] font-medium leading-relaxed transition-colors cursor-pointer text-white
                                           {{ $isGroupActive ? 'bg-white/15 text-white font-semibold' : 'hover:bg-white/10 text-white/90' }}">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <svg class="w-4 h-4 shrink-0 text-white/90" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        {!! $group['icon'] !!}
                                    </svg>
                                    <span class="truncate">{{ $group['label'] }}</span>
                                </div>
                                <svg class="w-3.5 h-3.5 shrink-0 transform transition-transform duration-150 text-white/70"
                                     :class="openCategories.{{ $groupKey }} ? 'rotate-90' : ''"
                                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>

                            {{-- Submenu Items (Text Only, Thin 1px White Guide Line) --}}
                            <div x-show="openCategories.{{ $groupKey }}"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="mt-1 ml-4 pl-3 border-l border-white/70 space-y-1">
                                @foreach($visibleItems as $item)
                                    @php $isItemActive = request()->routeIs($item['route'] . '*'); @endphp
                                    <a href="{{ route($item['route']) }}"
                                       class="block px-2.5 py-1.5 text-[13px] leading-relaxed rounded-md transition-colors text-white
                                              {{ $isItemActive
                                                  ? 'bg-white/20 font-semibold text-white'
                                                  : 'hover:bg-white/10 text-white/85 font-normal' }}">
                                        <span>{{ $item['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Collapsed Mode (Rail Icon Only) --}}
                        <div x-show="!sidebarOpen" style="display: none;" class="flex flex-col items-center">
                            <button type="button"
                                    @click="sidebarOpen = true; openCategories.{{ $groupKey }} = true"
                                    title="{{ $group['label'] }}"
                                    class="w-9 h-9 rounded-md flex items-center justify-center transition-colors cursor-pointer
                                           {{ $isGroupActive ? 'bg-white/20 text-white shadow-xs' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                                <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    {!! $group['icon'] !!}
                                </svg>
                            </button>
                        </div>
                    </div>

                    @if(!$loop->last)
                        <div x-show="!sidebarOpen" style="display: none;" class="w-6 mx-auto my-1 border-b border-white/15"></div>
                    @endif
                @endif
            @endforeach
        </nav>

        {{-- Collapse / Expand Sidebar (Single button at bottom left matching reference) --}}
        <div class="border-t border-white/10 flex items-center shrink-0 transition-all duration-200"
             :class="sidebarOpen ? 'px-2.5 py-2 justify-start' : 'p-2 justify-center'">
            <button type="button"
                    @click="sidebarOpen = !sidebarOpen"
                    class="p-1.5 text-white/75 hover:text-white hover:bg-white/10 rounded-md transition-colors cursor-pointer"
                    :title="sidebarOpen ? 'Perkecil sidebar' : 'Perluas sidebar'">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 4v16"/>
                </svg>
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 4v16"/>
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
                        {{-- User Header with Email --}}
                        <div class="px-4 py-3 bg-gray-50/70">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
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
