@extends('layouts.app')

@section('title', 'Laporan Evaluasi')
@section('page-title', 'Laporan Evaluasi')

@section('content')
<div class="py-4 space-y-6">

    {{-- Breadcrumb & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 pb-4">
        <div class="flex items-center space-x-2 text-xs text-gray-500">
            <a href="{{ route('reports.index') }}" class="hover:text-emerald-700 transition">Laporan</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">Evaluasi</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.export', request()->only(['period', 'recommendation'])) }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor CSV
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap gap-2 items-end bg-white border border-gray-200 p-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Periode (YYYY-MM)</label>
            <input type="month" name="period" value="{{ request('period') }}" class="input-base font-mono">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Rekomendasi</label>
            <select name="recommendation" class="input-base">
                <option value="">Semua Rekomendasi</option>
                <option value="lanjutkan_tingkatkan" {{ request('recommendation') === 'lanjutkan_tingkatkan' ? 'selected' : '' }}>Lanjutkan & Tingkatkan</option>
                <option value="pembinaan_khusus" {{ request('recommendation') === 'pembinaan_khusus' ? 'selected' : '' }}>Pembinaan Khusus</option>
                <option value="hentikan" {{ request('recommendation') === 'hentikan' ? 'selected' : '' }}>Hentikan Pembiayaan</option>
            </select>
        </div>
        <button type="submit" class="btn-secondary">
            Filter
        </button>
        @if(request()->hasAny(['period', 'recommendation']))
            <a href="{{ route('reports.evaluations') }}" class="btn-secondary text-gray-500">Reset</a>
        @endif
    </form>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @php
            $statItems = [
                ['label' => 'Total Evaluasi', 'value' => $stats['total']],
                ['label' => 'Rata-Rata Skor', 'value' => number_format($stats['avg_score'], 1)],
                ['label' => 'Skor Tertinggi', 'value' => number_format($stats['max_score'], 1)],
                ['label' => 'Skor Terendah', 'value' => number_format($stats['min_score'], 1)],
            ];
        @endphp
        @foreach($statItems as $stat)
            <div class="bg-white border border-gray-200 p-4">
                <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider block">{{ $stat['label'] }}</span>
                <p class="text-2xl font-semibold text-gray-900 font-mono mt-1 tracking-tight">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-200">
        @if($evaluations->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <p class="text-xs font-semibold text-gray-600">Tidak ada evaluasi yang ditemukan.</p>
                <p class="text-[11px] text-gray-400 mt-1">Coba sesuaikan filter pencarian periode atau rekomendasi.</p>
            </div>
        @else
            {{-- Desktop Table (hidden on mobile, unchanged for desktop) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left data-table">
                    <thead>
                        <tr>
                            <th>Usaha / Anggota</th>
                            <th class="text-center">Periode</th>
                            <th class="text-center">Skor Akhir</th>
                            <th class="text-center">Rekomendasi</th>
                            <th>Divalidasi Oleh</th>
                            <th>Tgl Validasi</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluations as $evaluation)
                            <tr>
                                <td>
                                    <p class="font-medium text-gray-900 text-xs">{{ $evaluation->business->name }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $evaluation->business->member->full_name }}</p>
                                </td>
                                <td class="text-center font-mono text-xs text-gray-600">
                                    {{ $evaluation->visit?->evaluation_period ?? '-' }}
                                </td>
                                <td class="text-center font-mono font-semibold text-xs text-gray-900">
                                    {{ number_format($evaluation->final_score, 1) }}
                                </td>
                                <td class="text-center">
                                    @if($evaluation->recommendation)
                                        <span class="badge border
                                            {{ $evaluation->recommendation->value === 'lanjutkan_tingkatkan' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                                               ($evaluation->recommendation->value === 'pembinaan_khusus' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-red-50 border-red-200 text-red-800') }}">
                                            {{ $evaluation->recommendation->label() }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-xs text-gray-600">{{ $evaluation->validatedBy?->name ?? '-' }}</td>
                                <td class="text-xs text-gray-500 font-mono whitespace-nowrap">
                                    {{ $evaluation->validated_at ? $evaluation->validated_at->format('d/m/Y') : '-' }}
                                </td>
                                <td class="text-right whitespace-nowrap text-xs">
                                    <a href="{{ route('evaluations.show', $evaluation) }}" class="font-semibold text-emerald-700 hover:text-emerald-900 underline">
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
                @foreach($evaluations as $evaluation)
                    <div class="p-4 space-y-3 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">
                                    {{ $evaluation->business->name }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $evaluation->business->member->full_name }}
                                </p>
                            </div>
                            @if($evaluation->recommendation)
                                <span class="badge border shrink-0
                                    {{ $evaluation->recommendation->value === 'lanjutkan_tingkatkan' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                                       ($evaluation->recommendation->value === 'pembinaan_khusus' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-red-50 border-red-200 text-red-800') }}">
                                    {{ $evaluation->recommendation->label() }}
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs bg-gray-50 p-2.5 border border-gray-100 font-mono">
                            <div>
                                <span class="text-[10px] text-gray-400 uppercase tracking-wider block font-sans">Skor Akhir</span>
                                <span class="font-bold text-sm text-gray-900">{{ number_format($evaluation->final_score, 1) }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-gray-400 uppercase tracking-wider block font-sans">Periode</span>
                                <span class="text-gray-700">{{ $evaluation->visit?->evaluation_period ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-500 pt-1">
                            <span>Oleh: {{ $evaluation->validatedBy?->name ?? '-' }}</span>
                            <span>{{ $evaluation->validated_at ? $evaluation->validated_at->format('d/m/Y') : '-' }}</span>
                        </div>

                        <div class="pt-2 border-t border-gray-100">
                            <a href="{{ route('evaluations.show', $evaluation) }}"
                               class="btn-secondary text-xs py-1.5 px-3 font-semibold text-emerald-800 border-emerald-300 hover:bg-emerald-50 w-full text-center justify-center">
                                Detail Evaluasi &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($evaluations->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $evaluations->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
