@extends('layouts.app')

@section('title', 'Daftar Pembinaan')
@section('page-title', 'Pembinaan Usaha')

@section('content')
<div class="py-4 space-y-4">

    {{-- Filter & Top Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2 items-center">
            <select name="status" onchange="this.form.submit()" class="input-base w-auto">
                <option value="">-- Semua Status --</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Belum Dimulai</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Berjalan</option>
                <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Selesai</option>
            </select>
            @if(request()->hasAny(['status']))
                <a href="{{ route('coaching.index') }}" class="btn-secondary text-gray-500 text-xs">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table Card --}}
    <div class="bg-white border border-gray-200">
        @if($recommendations->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs font-semibold text-gray-700">Belum ada rekomendasi pembinaan.</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Rekomendasi muncul setelah evaluasi disetujui / divalidasi oleh manajer.</p>
            </div>
        @else
            {{-- Desktop Table (hidden on mobile, unchanged for desktop) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left data-table">
                    <thead>
                        <tr>
                            <th>Usaha / Anggota</th>
                            <th>Kategori & Judul</th>
                            <th class="text-center">Status</th>
                            <th>Petugas / Pembuat</th>
                            <th>Waktu Terbit</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recommendations as $rec)
                            <tr>
                                <td>
                                    <a href="{{ route('businesses.show', $rec->evaluation->business) }}"
                                       class="font-medium text-gray-900 hover:text-emerald-700 transition block">
                                        {{ $rec->evaluation->business->name }}
                                    </a>
                                    <span class="text-xs text-gray-400">{{ $rec->evaluation->business->member->full_name }}</span>
                                </td>
                                <td>
                                    <span class="badge border border-gray-200 bg-gray-50 text-gray-700 mb-1">
                                        {{ $rec->category }}
                                    </span>
                                    <p class="font-medium text-gray-900 text-xs leading-snug">{{ $rec->title }}</p>
                                </td>
                                <td class="text-center">
                                    <span class="badge border
                                        {{ $rec->status->value === 'done' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                                           ($rec->status->value === 'in_progress' ? 'bg-blue-50 border-blue-200 text-blue-800' : 'bg-amber-50 border-amber-200 text-amber-800') }}">
                                        {{ $rec->status->label() }}
                                    </span>
                                </td>
                                <td class="text-xs text-gray-600">{{ $rec->createdBy?->name ?? '-' }}</td>
                                <td class="text-xs text-gray-500 font-mono whitespace-nowrap">
                                    {{ $rec->created_at->format('d/m/Y') }}
                                </td>
                                <td class="text-right whitespace-nowrap text-xs">
                                    <a href="{{ route('coaching.show', $rec) }}" class="font-semibold text-emerald-700 hover:text-emerald-900 underline">
                                        Detail &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card List (Khusus Tampilan HP / Petugas Lapangan) --}}
            <div class="block md:hidden divide-y divide-gray-200">
                @foreach($recommendations as $rec)
                    <div class="p-4 space-y-3 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="badge border border-gray-200 bg-gray-50 text-gray-700 text-[11px] mb-1">
                                    {{ $rec->category }}
                                </span>
                                <h3 class="font-bold text-gray-900 text-sm leading-snug">
                                    {{ $rec->title }}
                                </h3>
                                <p class="text-xs text-emerald-800 font-medium mt-1">
                                    {{ $rec->evaluation->business->name }}
                                    <span class="text-gray-400 font-normal">({{ $rec->evaluation->business->member->full_name }})</span>
                                </p>
                            </div>
                            <span class="badge border shrink-0
                                {{ $rec->status->value === 'done' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                                   ($rec->status->value === 'in_progress' ? 'bg-blue-50 border-blue-200 text-blue-800' : 'bg-amber-50 border-amber-200 text-amber-800') }}">
                                {{ $rec->status->label() }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-xs bg-gray-50 p-2.5 border border-gray-100">
                            <div>
                                <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Dibuat Oleh</span>
                                <span class="font-medium text-gray-700">{{ $rec->createdBy?->name ?? '-' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Waktu Terbit</span>
                                <span class="font-mono text-gray-700">{{ $rec->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-2 border-t border-gray-100">
                            <a href="{{ route('coaching.show', $rec) }}"
                               class="btn-secondary text-xs py-1.5 px-3 font-semibold text-emerald-800 border-emerald-300 hover:bg-emerald-50 w-full text-center justify-center">
                                Detail Pembinaan &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($recommendations->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $recommendations->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
