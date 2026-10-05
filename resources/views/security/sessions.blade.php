@extends('layouts.app')

@section('title', 'Sesi Aktif')
@section('page-title', 'Sesi Aktif')

@section('content')
<div class="py-4 space-y-6">

    {{-- Header --}}
    <div class="border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900 tracking-tight">Manajemen Sesi Aktif</h2>
            <p class="text-xs text-gray-500 mt-0.5">Pantau dan cabut sesi pengguna yang aktif atau mencurigakan</p>
        </div>
        @if($canViewAll && $sessions->count() > 1)
            <form method="POST" action="{{ route('security.sessions.revoke-all') }}"
                  onsubmit="return confirm('Yakin mencabut SEMUA sesi (kecuali sesi Anda)? Seluruh pengguna akan ter-logout.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cabut Semua Sesi
                </button>
            </form>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 text-xs px-4 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if($sessionDriver !== 'database')
        {{-- Session driver info --}}
        <div class="bg-amber-50 border border-amber-200 px-4 py-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-xs text-amber-800">
                    <p class="font-semibold mb-1">Session Driver: <code class="font-mono bg-amber-100 px-1">{{ $sessionDriver }}</code></p>
                    <p>Manajemen sesi aktif hanya tersedia saat <code class="font-mono bg-amber-100 px-1">SESSION_DRIVER=database</code>.</p>
                    <p class="mt-1">Untuk mengaktifkan, ubah di file <code class="font-mono bg-amber-100 px-1">.env</code>:
                        <code class="font-mono bg-amber-100 px-1">SESSION_DRIVER=database</code>,
                        lalu jalankan <code class="font-mono bg-amber-100 px-1">php artisan session:table && php artisan migrate</code>.
                    </p>
                </div>
            </div>
        </div>
    @else

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 p-4">
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Sesi Aktif</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalActive }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Sesi dalam masa aktif</p>
            </div>
            <div class="bg-white border border-gray-200 p-4">
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Sesi Kadaluarsa</p>
                <p class="text-2xl font-bold text-gray-400 mt-1">{{ $totalExpired }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Belum dibersihkan GC</p>
            </div>
            <div class="bg-white border border-gray-200 p-4">
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Pengguna Unik</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalUsers }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">User dengan sesi tercatat</p>
            </div>
        </div>

        {{-- Sessions Table --}}
        <div class="bg-white border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="text-xs font-semibold text-gray-700">
                    Daftar Sesi
                    @if($canViewAll)
                        <span class="font-normal text-gray-400">(Semua Pengguna)</span>
                    @else
                        <span class="font-normal text-gray-400">(Sesi Anda)</span>
                    @endif
                </h3>
            </div>

            @if($sessions->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Pengguna</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">IP Address</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Perangkat</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Aktivitas Terakhir</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Status</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($sessions as $session)
                                @php
                                    $isCurrentSession = $session->session_id === $currentSessionId;
                                    $ua = $session->user_agent ?? '';
                                    // Simple UA parsing
                                    $browser = str_contains($ua, 'Chrome') ? 'Chrome'
                                        : (str_contains($ua, 'Firefox') ? 'Firefox'
                                        : (str_contains($ua, 'Safari') ? 'Safari'
                                        : (str_contains($ua, 'Edge') ? 'Edge'
                                        : 'Browser Lain')));
                                    $os = str_contains($ua, 'Windows') ? 'Windows'
                                        : (str_contains($ua, 'Mac') ? 'macOS'
                                        : (str_contains($ua, 'Linux') ? 'Linux'
                                        : (str_contains($ua, 'Android') ? 'Android'
                                        : (str_contains($ua, 'iPhone') ? 'iOS'
                                        : 'OS Lain'))));
                                    $isMobile = str_contains($ua, 'Mobile') || str_contains($ua, 'Android') || str_contains($ua, 'iPhone');
                                @endphp
                                <tr class="hover:bg-gray-50 {{ $isCurrentSession ? 'bg-emerald-50/50' : '' }}">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($session->user_name)
                                            <div class="font-medium text-gray-900">{{ $session->user_name }}</div>
                                            <div class="text-[11px] text-gray-400 font-mono">{{ $session->user_email }}</div>
                                        @else
                                            <span class="text-gray-400 italic text-[11px]">Tamu / Tidak Terautentikasi</span>
                                        @endif
                                        @if($isCurrentSession)
                                            <span class="inline-block mt-0.5 px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-semibold">SESI INI</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap font-mono text-[11px] text-gray-700">
                                        {{ $session->ip_address ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                        <div class="flex items-center gap-1.5">
                                            @if($isMobile)
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                            @else
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            @endif
                                            <span class="text-[11px]">{{ $browser }} / {{ $os }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                        <div class="text-[11px]">{{ $session->last_activity_dt->format('d/m/Y H:i') }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $session->last_activity_dt->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($session->is_expired)
                                            <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-500 text-[10px] uppercase font-mono border border-gray-200">Kadaluarsa</span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] uppercase font-mono border border-emerald-200">Aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if(!$isCurrentSession)
                                            <form method="POST"
                                                  action="{{ route('security.sessions.revoke', $session->session_id) }}"
                                                  onsubmit="return confirm('Yakin cabut sesi ini? Pengguna akan ter-logout.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 border border-red-300 text-red-600 text-[11px] font-medium hover:bg-red-50 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                                                    </svg>
                                                    Cabut
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] text-gray-300 italic">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-4 py-12 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-500">Tidak ada data sesi</p>
                    <p class="text-xs text-gray-400 mt-1">Pastikan session driver sudah diatur ke <code class="font-mono">database</code></p>
                </div>
            @endif
        </div>

        {{-- Note about session lifetime --}}
        <div class="text-[11px] text-gray-400 text-right">
            Sesi kadaluarsa setelah <strong>{{ config('session.lifetime', 120) }} menit</strong> tidak aktif.
            Nilai ini dikonfigurasi di <code class="font-mono">.env → SESSION_LIFETIME</code>.
        </div>

    @endif

</div>
@endsection
