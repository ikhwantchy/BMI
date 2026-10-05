@extends('layouts.app')

@section('title', 'Dashboard Petugas')
@section('page-title', 'Dashboard Operasional')

@section('content')
<div class="py-4 space-y-6">

    {{-- Greeting Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-200 pb-4">
        <div>
            <h2 class="text-base font-semibold text-gray-900 tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">Petugas Pendamping Lapangan Koperasi BMI</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('visits.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Jadwalkan Kunjungan
            </a>
            <a href="{{ route('members.create') }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Tambah Anggota
            </a>
        </div>
    </div>

    {{-- Stats Row (Subtle BMI Accent Tops) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white border border-gray-200 border-t-2 border-t-[#009a4c] p-4">
            <div class="flex items-center justify-between text-gray-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Anggota Binaan</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-2xl font-semibold text-gray-900 tracking-tight">{{ $totalMembers }}</p>
            <p class="text-[11px] text-gray-500 mt-1">Total binaan terdaftar</p>
        </div>

        <div class="bg-white border border-gray-200 border-t-2 border-t-[#00a1e8] p-4">
            <div class="flex items-center justify-between text-gray-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Kunjungan Hari Ini</span>
                <span class="px-1.5 py-0.5 text-[10px] font-semibold bg-[#f0f9ff] text-[#0084be] border border-[#00a1e8]">AKTIF</span>
            </div>
            <p class="text-2xl font-semibold text-gray-900 tracking-tight">{{ $todayVisits->count() }}</p>
            <p class="text-[11px] text-gray-500 mt-1">Terjadwal hari ini</p>
        </div>

        <div class="bg-white border border-gray-200 border-t-2 border-t-[#e4c85b] p-4">
            <div class="flex items-center justify-between text-gray-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Menunggu Visit</span>
                <span class="px-1.5 py-0.5 text-[10px] font-semibold bg-[#fefce8] text-[#9c7c10] border border-[#e4c85b] font-mono">{{ $pendingVisits }}</span>
            </div>
            <p class="text-2xl font-semibold text-gray-900 tracking-tight">{{ $pendingVisits }}</p>
            <p class="text-[11px] text-gray-500 mt-1">Antrean rencana kunjungan</p>
        </div>

        <div class="bg-white border border-gray-200 border-t-2 border-t-gray-400 p-4">
            <div class="flex items-center justify-between text-gray-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Evaluasi Draft</span>
                @if($incompleteEvaluations > 0)
                    <span class="px-1.5 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-300 font-mono">{{ $incompleteEvaluations }}</span>
                @endif
            </div>
            <p class="text-2xl font-semibold text-gray-900 tracking-tight">{{ $incompleteEvaluations }}</p>
            <p class="text-[11px] text-gray-500 mt-1">Perlu input / diajukan</p>
        </div>

    </div>

    {{-- Today's Schedule Table --}}
    <div class="bg-white border border-gray-200">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50/50">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xs font-semibold text-gray-800 uppercase tracking-wider">Jadwal Kunjungan Hari Ini</h3>
            </div>
            <a href="{{ route('visits.create') }}" class="text-xs font-semibold text-[#009a4c] hover:text-[#007d3e]">
                + Jadwal Baru
            </a>
        </div>

        @if($todayVisits->isEmpty())
            <div class="text-center py-12 text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-xs text-gray-500">Tidak ada kunjungan lapangan terjadwal hari ini.</p>
                <a href="{{ route('visits.create') }}" class="inline-block mt-2 text-xs font-semibold text-[#009a4c] hover:underline">
                    Buat jadwal kunjungan &rarr;
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left data-table">
                    <thead>
                        <tr>
                            <th>Anggota</th>
                            <th>Usaha &amp; Lokasi</th>
                            <th>Status Kunjungan</th>
                            <th class="text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayVisits as $visit)
                            <tr>
                                <td class="font-medium text-gray-900">
                                    {{ $visit->business->member->full_name }}
                                    <div class="text-xs text-gray-400 font-mono">{{ $visit->business->member->member_number }}</div>
                                </td>
                                <td>
                                    <div class="text-gray-900 font-medium">{{ $visit->business->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $visit->business->business_type }}</div>
                                </td>
                                <td>
                                    <span class="badge border bg-{{ $visit->status->badgeColor() }}-50 border-{{ $visit->status->badgeColor() }}-200 text-{{ $visit->status->badgeColor() }}-800">
                                        {{ $visit->status->label() }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('visits.show', $visit) }}"
                                       class="text-xs font-semibold text-[#009a4c] hover:text-[#007d3e] underline">
                                        Lihat Detail &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
