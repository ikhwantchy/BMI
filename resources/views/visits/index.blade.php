@extends('layouts.app')

@section('title', auth()->user()->hasRole('petugas_lapangan') ? 'Tugas Kunjungan' : 'Jadwal & Kunjungan Lapangan')
@section('page-title', auth()->user()->hasRole('petugas_lapangan') ? 'Tugas Kunjungan' : 'Kunjungan Lapangan')

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
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Ditugaskan</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai / Menunggu Validasi</option>
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

        @can('create', App\Models\Visit::class)
        <a href="{{ route('visits.create') }}" class="btn-primary w-full sm:w-auto justify-center shrink-0 drawer-link">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Jadwalkan Kunjungan
        </a>
        @endcan
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
                                <div class="flex items-center justify-end gap-2">
                                    @if(auth()->user()->isOfficer() || auth()->user()->hasRole('petugas_lapangan'))
                                        @if($visit->status->value === 'scheduled')
                                            <a href="{{ route('visits.show', $visit) }}" class="btn-primary text-xs py-1 px-2.5">
                                                Mulai Kunjungan &rarr;
                                            </a>
                                        @elseif($visit->status->value === 'in_progress')
                                            <a href="{{ route('visits.show', $visit) }}" class="btn-primary bg-amber-600 hover:bg-amber-700 text-xs py-1 px-2.5">
                                                Lanjutkan &rarr;
                                            </a>
                                        @elseif($visit->status->value === 'needs_revision')
                                            <a href="{{ route('visits.show', $visit) }}" class="btn-primary bg-orange-600 hover:bg-orange-700 text-xs py-1 px-2.5">
                                                Revisi &rarr;
                                            </a>
                                        @else
                                            <a href="{{ route('visits.show', $visit) }}" class="font-semibold text-emerald-700 hover:text-emerald-900 underline">
                                                Detail
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('visits.show', $visit) }}" class="font-semibold text-emerald-700 hover:text-emerald-900 underline">
                                            Detail
                                        </a>
                                        @can('update', $visit)
                                            <a href="{{ route('visits.edit', $visit) }}" class="font-semibold text-gray-600 hover:text-gray-900 drawer-link">
                                                Edit
                                            </a>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400">
                                <p class="text-xs text-gray-500">
                                    {{ (auth()->user()->isOfficer() || auth()->user()->hasRole('petugas_lapangan')) ? 'Belum ada tugas kunjungan yang ditugaskan kepada Anda.' : 'Belum ada kunjungan lapangan tercatat.' }}
                                </p>
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
                        @if(auth()->user()->isOfficer() || auth()->user()->hasRole('petugas_lapangan'))
                            @if($visit->status->value === 'scheduled')
                                <a href="{{ route('visits.show', $visit) }}"
                                   class="btn-primary text-xs py-1.5 px-3.5 flex items-center gap-1.5 font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Mulai Kunjungan
                                </a>
                            @elseif($visit->status->value === 'in_progress')
                                <a href="{{ route('visits.show', $visit) }}"
                                   class="btn-primary bg-amber-600 hover:bg-amber-700 text-xs py-1.5 px-3.5 flex items-center gap-1.5 font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Lanjutkan Kunjungan
                                </a>
                            @elseif($visit->status->value === 'needs_revision')
                                <a href="{{ route('visits.show', $visit) }}"
                                   class="btn-primary bg-orange-600 hover:bg-orange-700 text-xs py-1.5 px-3.5 flex items-center gap-1.5 font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Revisi Kunjungan
                                </a>
                            @else
                                <a href="{{ route('visits.show', $visit) }}"
                                   class="btn-secondary text-xs py-1.5 px-3 font-semibold text-emerald-800 border-emerald-300 hover:bg-emerald-50">
                                    Lihat Detail
                                </a>
                            @endif
                        @else
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
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400">
                    <p class="text-xs text-gray-500">
                        {{ (auth()->user()->isOfficer() || auth()->user()->hasRole('petugas_lapangan')) ? 'Belum ada tugas kunjungan yang ditugaskan kepada Anda.' : 'Belum ada kunjungan lapangan tercatat.' }}
                    </p>
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
