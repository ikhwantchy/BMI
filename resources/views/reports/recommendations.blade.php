@extends('layouts.app')

@section('title', 'Distribusi Rekomendasi')
@section('page-title', 'Distribusi Rekomendasi')

@section('content')
<div class="py-4 space-y-6">

    {{-- Breadcrumb & Top Bar --}}
    <div class="flex items-center space-x-2 text-xs text-gray-500 border-b border-gray-200 pb-3">
        <a href="{{ route('reports.index') }}" class="hover:text-emerald-700 transition">Laporan</a>
        <span>/</span>
        <span class="text-gray-900 font-semibold">Distribusi Rekomendasi</span>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap gap-2 items-end bg-white border border-gray-200 p-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Periode (YYYY-MM)</label>
            <input type="month" name="period" value="{{ request('period') }}" class="input-base font-mono">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Filter Rekomendasi</label>
            <select name="recommendation" class="input-base">
                <option value="">Semua</option>
                <option value="lanjutkan_tingkatkan" {{ request('recommendation') === 'lanjutkan_tingkatkan' ? 'selected' : '' }}>Lanjutkan & Tingkatkan</option>
                <option value="pembinaan_khusus" {{ request('recommendation') === 'pembinaan_khusus' ? 'selected' : '' }}>Pembinaan Khusus</option>
                <option value="hentikan" {{ request('recommendation') === 'hentikan' ? 'selected' : '' }}>Hentikan Pembiayaan</option>
            </select>
        </div>
        <button type="submit" class="btn-secondary">
            Filter
        </button>
        @if(request()->hasAny(['period', 'recommendation']))
            <a href="{{ route('reports.recommendations') }}" class="btn-secondary text-gray-500">Reset</a>
        @endif
    </form>

    {{-- Distribusi Cards --}}
    @php
        $distDefs = [
            'lanjutkan_tingkatkan' => [
                'label'   => 'Lanjutkan & Tingkatkan',
                'desc'    => 'Skor ≥ 80 — Usaha sehat, prioritas ekspansi & plafond',
                'badge'   => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                'bar'     => 'bg-emerald-700',
            ],
            'pembinaan_khusus' => [
                'label'   => 'Pembinaan Khusus',
                'desc'    => 'Skor 60–79.9 — Butuh kunjungan intensif & pelatihan',
                'badge'   => 'bg-amber-50 border-amber-200 text-amber-800',
                'bar'     => 'bg-amber-500',
            ],
            'hentikan' => [
                'label'   => 'Hentikan Pembiayaan',
                'desc'    => 'Skor < 60 — Risiko tinggi, restrukturisasi diperlukan',
                'badge'   => 'bg-red-50 border-red-200 text-red-800',
                'bar'     => 'bg-red-600',
            ],
        ];
        $totalDist = $distribution->sum('total');
        $distKeyed = $distribution->keyBy('recommendation');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($distDefs as $key => $def)
            @php $count = $distKeyed->get($key)?->total ?? 0; $pct = $totalDist > 0 ? round($count / $totalDist * 100) : 0; @endphp
            <div class="bg-white border border-gray-200 p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="badge border {{ $def['badge'] }}">{{ $def['label'] }}</span>
                    <span class="text-2xl font-semibold text-gray-900 font-mono">{{ $count }}</span>
                </div>
                <p class="text-[11px] text-gray-500 leading-normal">{{ $def['desc'] }}</p>
                <div class="pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                        <span>Proporsi</span>
                        <span class="font-mono font-semibold text-gray-900">{{ $pct }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 h-1.5 overflow-hidden">
                        <div class="{{ $def['bar'] }} h-full transition-all duration-300"
                             style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1.5">dari {{ $totalDist }} total evaluasi</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Detail Tabel Evaluasi Terkait --}}
    <div class="bg-white border border-gray-200">
        <div class="p-4 border-b border-gray-200 bg-gray-50/50">
            <h3 class="font-semibold text-gray-900 text-xs uppercase tracking-wider">Detail Evaluasi Usaha</h3>
            <p class="text-[11px] text-gray-500 mt-0.5">Daftar evaluasi tervalidasi berdasarkan kategori rekomendasi</p>
        </div>

        @if($recentEvaluations->isEmpty())
            <div class="py-12 text-center text-gray-400">
                <p class="text-xs text-gray-500">Tidak ada evaluasi untuk kategori ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left data-table">
                    <thead>
                        <tr>
                            <th>Usaha</th>
                            <th>Anggota</th>
                            <th class="text-center">Skor</th>
                            <th>Rekomendasi</th>
                            <th>Divalidasi Oleh</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEvaluations as $eval)
                            <tr>
                                <td>
                                    <a href="{{ route('businesses.show', $eval->business) }}" class="font-medium text-gray-900 hover:text-emerald-700">
                                        {{ $eval->business->name }}
                                    </a>
                                </td>
                                <td class="text-xs text-gray-600">
                                    {{ $eval->business->member->full_name }}
                                </td>
                                <td class="text-center font-mono font-semibold text-xs text-gray-900">
                                    {{ number_format($eval->final_score, 1) }}
                                </td>
                                <td>
                                    @if($eval->recommendation)
                                        <span class="badge border
                                            {{ $eval->recommendation->value === 'lanjutkan_tingkatkan' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                                               ($eval->recommendation->value === 'pembinaan_khusus' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-red-50 border-red-200 text-red-800') }}">
                                            {{ $eval->recommendation->label() }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-xs text-gray-600">{{ $eval->validatedBy?->name ?? '-' }}</td>
                                <td class="text-right text-xs">
                                    <a href="{{ route('evaluations.show', $eval) }}" class="font-semibold text-emerald-700 hover:text-emerald-900 underline">
                                        Detail &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($recentEvaluations->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $recentEvaluations->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
