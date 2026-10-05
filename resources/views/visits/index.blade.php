@extends('layouts.app')

@section('title', 'Jadwal & Kunjungan Lapangan')
@section('page-title', 'Kunjungan Lapangan')

@section('content')
<div class="py-4 space-y-4">

    {{-- Filter & Actions Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="{{ route('visits.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2 flex-1">
            <input type="date"
                   name="date"
                   value="{{ request('date') }}"
                   class="input-base w-full sm:w-auto font-mono">
            <div class="flex items-center gap-2">
                <select name="status" class="input-base flex-1 sm:w-auto">
                    <option value="">-- Semua Status --</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Dijadwalkan</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <button type="submit" class="btn-secondary">
                    Filter
                </button>
                @if(request()->hasAny(['date', 'status']))
                    <a href="{{ route('visits.index') }}" class="btn-secondary text-gray-500">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <a href="{{ route('visits.create') }}" class="btn-primary w-full sm:w-auto justify-center shrink-0 drawer-link">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Jadwalkan Kunjungan
        </a>
    </div>

    {{-- Visits Table & Mobile Cards --}}
    <div class="bg-white border border-gray-200">
        {{-- Desktop Table (hidden on mobile, unchanged for desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left data-table">
                <thead>
                    <tr>
                        <th>Tanggal Visit</th>
                        <th>Usaha & Anggota</th>
                        <th>Periode</th>
                        <th>Petugas Lapangan</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visits as $visit)
                        <tr>
                            <td>
                                <span class="font-semibold text-gray-900 font-mono text-xs block">
                                    {{ $visit->visit_date->translatedFormat('d M Y') }}
                                </span>
                                <span class="text-[11px] text-gray-400">
                                    {{ $visit->visit_date->diffForHumans() }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('businesses.show', $visit->business) }}" class="font-medium text-gray-900 hover:text-emerald-700 block">
                                    {{ $visit->business->name }}
                                </a>
                                <p class="text-xs text-gray-500">
                                    {{ $visit->business->member->full_name }} <span class="font-mono text-[11px] text-gray-400">({{ $visit->business->member->member_number }})</span>
                                </p>
                            </td>
                            <td>
                                <span class="badge border border-gray-200 bg-gray-50 font-mono text-gray-700">
                                    {{ $visit->evaluation_period }}
                                </span>
                            </td>
                            <td class="text-gray-700 text-xs font-medium">
                                {{ $visit->officer?->name ?: '-' }}
                            </td>
                            <td>
                                <span class="badge border bg-{{ $visit->status->badgeColor() }}-50 border-{{ $visit->status->badgeColor() }}-200 text-{{ $visit->status->badgeColor() }}-800">
                                    {{ $visit->status->label() }}
                                </span>
                            </td>
                            <td class="text-right whitespace-nowrap text-xs">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('visits.show', $visit) }}" class="font-semibold text-emerald-700 hover:text-emerald-900 underline">
                                        Detail
                                    </a>
                                    @can('update', $visit)
                                        <a href="{{ route('visits.edit', $visit) }}" class="font-semibold text-gray-600 hover:text-gray-900 drawer-link">
                                            Edit
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400">
                                <p class="text-xs text-gray-500">Belum ada kunjungan lapangan tercatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card List (Khusus Tampilan HP / Petugas Lapangan) --}}
        <div class="block md:hidden divide-y divide-gray-200">
            @forelse($visits as $visit)
                <div class="p-4 space-y-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-bold text-gray-900 font-mono text-xs">
                                    {{ $visit->visit_date->translatedFormat('d M Y') }}
                                </span>
                                <span class="text-[11px] text-gray-400 font-mono">({{ $visit->visit_date->diffForHumans() }})</span>
                            </div>
                            <h3 class="font-semibold text-gray-900 text-sm mt-1.5">
                                <a href="{{ route('businesses.show', $visit->business) }}" class="hover:text-emerald-700">
                                    {{ $visit->business->name }}
                                </a>
                            </h3>
                            <p class="text-xs text-gray-600 mt-0.5">
                                {{ $visit->business->member->full_name }}
                                <span class="font-mono text-gray-400 text-[11px]">({{ $visit->business->member->member_number }})</span>
                            </p>
                        </div>
                        <span class="badge border shrink-0 bg-{{ $visit->status->badgeColor() }}-50 border-{{ $visit->status->badgeColor() }}-200 text-{{ $visit->status->badgeColor() }}-800">
                            {{ $visit->status->label() }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs bg-gray-50 p-2.5 border border-gray-100">
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Periode</span>
                            <span class="font-mono font-semibold text-gray-800">{{ $visit->evaluation_period }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Petugas</span>
                            <span class="font-medium text-gray-700">{{ $visit->officer?->name ?: '-' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <a href="{{ route('visits.show', $visit) }}"
                           class="btn-secondary text-xs py-1.5 px-3 font-semibold text-emerald-800 border-emerald-300 hover:bg-emerald-50">
                            Detail Kunjungan
                        </a>
                        @can('update', $visit)
                            <a href="{{ route('visits.edit', $visit) }}"
                               class="btn-secondary text-xs py-1.5 px-3 drawer-link">
                                Edit
                            </a>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400">
                    <p class="text-xs text-gray-500">Belum ada kunjungan lapangan tercatat.</p>
                </div>
            @endforelse
        </div>

        @if($visits->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $visits->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
