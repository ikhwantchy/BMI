@extends('layouts.app')

@section('title', 'Log Keamanan')
@section('page-title', 'Log Keamanan')

@section('content')
<div class="py-4 space-y-6">

    {{-- Header --}}
    <div class="border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900 tracking-tight">Log Keamanan & Percobaan Masuk</h2>
            <p class="text-xs text-gray-500 mt-0.5">Pantau aktivitas mencurigakan, brute force, dan percobaan login gagal</p>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 text-xs px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Failed Logins --}}
        <div class="bg-white border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Login Gagal</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($totalFailed) }}</p>
                    <p class="text-[11px] text-gray-400 mt-1">Percobaan masuk ditolak</p>
                </div>
                <div class="w-9 h-9 bg-red-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Successful Logins --}}
        <div class="bg-white border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Login Berhasil</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($totalLogin) }}</p>
                    <p class="text-[11px] text-gray-400 mt-1">Sesi berhasil dibuka</p>
                </div>
                <div class="w-9 h-9 bg-emerald-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Logouts --}}
        <div class="bg-white border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Logout</p>
                    <p class="text-2xl font-bold text-gray-700 mt-1">{{ number_format($totalLogout) }}</p>
                    <p class="text-[11px] text-gray-400 mt-1">Sesi ditutup manual</p>
                </div>
                <div class="w-9 h-9 bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Unique Attack IPs --}}
        <div class="bg-white border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">IP Penyerang Unik</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($uniqueIps) }}</p>
                    <p class="text-[11px] text-gray-400 mt-1">Alamat IP berbeda</p>
                </div>
                <div class="w-9 h-9 bg-orange-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Top Attacker IPs --}}
        @if($topAttackerIps->count())
        <div class="bg-white border border-gray-200 lg:col-span-1">
            <div class="px-4 py-3 border-b border-gray-100 bg-red-50">
                <h3 class="text-xs font-semibold text-red-800 uppercase tracking-wider">⚠ IP Penyerang Terbanyak</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($topAttackerIps as $i => $ip)
                <div class="px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-bold text-red-500 w-4">{{ $i + 1 }}</span>
                        <span class="font-mono text-xs text-gray-800">{{ $ip->ip_address }}</span>
                    </div>
                    <span class="inline-block px-2 py-0.5 bg-red-100 text-red-800 text-[11px] font-semibold">
                        {{ $ip->total }}x
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Filter & Log Table --}}
        <div class="bg-white border border-gray-200 {{ $topAttackerIps->count() ? 'lg:col-span-2' : 'lg:col-span-3' }}">
            {{-- Filter --}}
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                <form method="GET" action="{{ route('security.logs') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                    @if(auth()->user()->hasAnyRole(['system_admin', 'pengurus', 'pengawas']))
                        <select name="branch_id" class="text-xs border border-gray-300 px-2 py-1.5 focus:border-[#006633] focus:ring-0">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->code }} — {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <select name="action" class="text-xs border border-gray-300 px-2 py-1.5 focus:border-[#006633] focus:ring-0">
                        <option value="">Semua Jenis</option>
                        @foreach($actions as $key => $label)
                            <option value="{{ $key }}" {{ request('action') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="text-xs border border-gray-300 px-2 py-1.5 focus:border-[#006633] focus:ring-0" placeholder="Dari tanggal">

                    <div class="flex gap-1.5">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="IP, user..." class="w-full text-xs border border-gray-300 px-2 py-1.5 focus:border-[#006633] focus:ring-0">
                        <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white text-xs font-medium hover:bg-black transition-colors shrink-0">Cari</button>
                        @if(request()->anyFilled(['branch_id','action','date_from','search']))
                            <a href="{{ route('security.logs') }}" class="px-2 py-1.5 border border-gray-300 text-gray-600 text-xs hover:bg-gray-50 shrink-0">✕</a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Waktu</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Status</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Pengguna / Identifier</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">IP Address</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            @php
                                $rowBg = $log->action === 'failed_login' ? 'bg-red-50/60' : '';
                                $badge = match($log->action) {
                                    'failed_login' => 'bg-red-100 text-red-800 border-red-300 font-bold',
                                    'login'        => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    'logout'       => 'bg-gray-100 text-gray-700 border-gray-300',
                                    'role_changed' => 'bg-purple-100 text-purple-800 border-purple-300',
                                    default        => 'bg-gray-50 text-gray-700 border-gray-200',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 {{ $rowBg }}">
                                <td class="px-4 py-3 whitespace-nowrap font-mono text-[11px] text-gray-500">
                                    <div>{{ $log->created_at->format('d/m/Y') }}</div>
                                    <div class="text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-block px-2 py-0.5 text-[10px] uppercase font-mono tracking-wider border {{ $badge }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($log->user)
                                        <div class="font-medium text-gray-900">{{ $log->user->name }}</div>
                                        <div class="text-[11px] text-gray-400 font-mono">{{ $log->user->email }}</div>
                                    @else
                                        <span class="text-gray-400 italic text-[11px]">Tamu / Tidak Dikenal</span>
                                        @if(isset($log->old_values['attempted_identifier']))
                                            <div class="text-[10px] text-red-600 font-mono">Percobaan: {{ $log->old_values['attempted_identifier'] }}</div>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap font-mono text-[11px] text-gray-700">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-[11px]">
                                    {{ $log->context ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400 italic">
                                    Tidak ada log keamanan yang sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="border-t border-gray-200 p-3 bg-gray-50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
