@extends('layouts.app')

@section('title', 'Laporan Ringkasan')
@section('page-title', 'Laporan & Analitik')

@section('content')
<div class="py-4 space-y-6">

    {{-- Header --}}
    <div class="border-b border-gray-200 pb-4">
        <h2 class="text-base font-semibold text-gray-900 tracking-tight">Ringkasan Eksekutif</h2>
        <p class="text-xs text-gray-500 mt-0.5">Gambaran umum kinerja evaluasi dan pembinaan usaha anggota</p>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
            $kpis = [
                ['label' => 'Total Evaluasi', 'value' => $totalEvaluations],
                ['label' => 'Sudah Divalidasi', 'value' => $validatedCount],
                ['label' => 'Anggota Aktif', 'value' => $totalMembers],
                ['label' => 'Usaha Aktif', 'value' => $totalBusinesses],
                ['label' => 'Total Kunjungan', 'value' => $totalVisits],
                ['label' => 'Rata-Rata Skor', 'value' => number_format($avgScore, 1)],
            ];
        @endphp
        @foreach($kpis as $kpi)
            <div class="bg-white border border-gray-200 p-4">
                <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider block">{{ $kpi['label'] }}</span>
                <p class="text-2xl font-semibold text-gray-900 font-mono mt-1 tracking-tight">{{ $kpi['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Distribusi Rekomendasi + Quick Links --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Distribusi Rekomendasi --}}
        <div class="bg-white border border-gray-200 p-6 space-y-4">
            <div class="border-b border-gray-200 pb-3">
                <h3 class="font-semibold text-gray-900 text-xs uppercase tracking-wider">Distribusi Rekomendasi</h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Dari total evaluasi tervalidasi</p>
            </div>
            @php
                $distLabels = [
                    'lanjutkan_tingkatkan' => ['label' => 'Lanjutkan & Tingkatkan', 'color' => 'bg-emerald-700', 'badge' => 'bg-emerald-50 border-emerald-200 text-emerald-800'],
                    'pembinaan_khusus'     => ['label' => 'Pembinaan Khusus', 'color' => 'bg-amber-500', 'badge' => 'bg-amber-50 border-amber-200 text-amber-800'],
                    'hentikan'             => ['label' => 'Hentikan Pembiayaan', 'color' => 'bg-red-600', 'badge' => 'bg-red-50 border-red-200 text-red-800'],
                ];
                $totalDist = $distribution->sum('total');
            @endphp
            @if($distribution->isEmpty())
                <p class="text-xs text-gray-400 italic text-center py-8">Belum ada data distribusi.</p>
            @else
                <div class="space-y-4">
                    @foreach($distLabels as $key => $info)
                        @php $count = $distribution->get($key)?->total ?? 0; $pct = $totalDist > 0 ? round($count / $totalDist * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5 text-xs">
                                <span class="font-medium text-gray-800">{{ $info['label'] }}</span>
                                <span class="badge border font-mono {{ $info['badge'] }}">
                                    {{ $count }} ({{ $pct }}%)
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 h-1.5 overflow-hidden">
                                <div class="{{ $info['color'] }} h-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Quick Navigation --}}
        <div class="bg-white border border-gray-200 p-6 space-y-4">
            <div class="border-b border-gray-200 pb-3">
                <h3 class="font-semibold text-gray-900 text-xs uppercase tracking-wider">Menu Laporan</h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Akses laporan detail dan ekspor data</p>
            </div>
            <div class="space-y-2">
                <a href="{{ route('reports.evaluations') }}"
                   class="flex items-center justify-between p-3.5 border border-gray-200 hover:border-gray-400 hover:bg-gray-50 transition group">
                    <div>
                        <p class="font-semibold text-gray-900 text-xs uppercase tracking-wider">Laporan Evaluasi</p>
                        <p class="text-xs text-gray-500 mt-0.5">Daftar evaluasi tervalidasi dengan filter periode</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-emerald-700 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="{{ route('reports.recommendations') }}"
                   class="flex items-center justify-between p-3.5 border border-gray-200 hover:border-gray-400 hover:bg-gray-50 transition group">
                    <div>
                        <p class="font-semibold text-gray-900 text-xs uppercase tracking-wider">Distribusi Rekomendasi</p>
                        <p class="text-xs text-gray-500 mt-0.5">Analisis rekomendasi per periode evaluasi</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-emerald-700 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="{{ route('reports.export') }}"
                   class="flex items-center justify-between p-3.5 border border-gray-200 hover:border-gray-400 hover:bg-gray-50 transition group">
                    <div>
                        <p class="font-semibold text-gray-900 text-xs uppercase tracking-wider">Ekspor CSV</p>
                        <p class="text-xs text-gray-500 mt-0.5">Unduh data evaluasi dalam format spreadsheet</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-emerald-700 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </a>
            </div>
        </div>

    </div>

    {{-- Tren Bulanan --}}
    <div class="bg-white border border-gray-200">
        <div class="p-4 border-b border-gray-200 bg-gray-50/50">
            <h3 class="font-semibold text-gray-900 text-xs uppercase tracking-wider">Tren Evaluasi (12 Bulan Terakhir)</h3>
            <p class="text-[11px] text-gray-500 mt-0.5">Jumlah evaluasi dan rata-rata skor per bulan</p>
        </div>
        @if($monthlyTrend->isEmpty())
            <p class="text-xs text-gray-400 italic text-center py-8">Belum ada data tren.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left data-table">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th class="text-center">Jumlah Evaluasi</th>
                            <th class="text-center">Rata-Rata Skor</th>
                            <th>Visualisasi Proporsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $maxTotal = $monthlyTrend->max('total') ?: 1; @endphp
                        @foreach($monthlyTrend as $trend)
                            <tr>
                                <td class="font-mono text-xs font-semibold text-gray-900">{{ $trend->month }}</td>
                                <td class="text-center font-mono font-semibold text-gray-800">{{ $trend->total }}</td>
                                <td class="text-center">
                                    <span class="font-mono text-xs font-semibold
                                        {{ $trend->avg_score >= 80 ? 'text-emerald-800' : ($trend->avg_score >= 60 ? 'text-amber-800' : 'text-red-800') }}">
                                        {{ number_format($trend->avg_score, 1) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="w-full max-w-xs bg-gray-100 h-1.5 overflow-hidden">
                                        <div class="bg-emerald-700 h-full"
                                             style="width: {{ ($trend->total / $maxTotal) * 100 }}%"></div>
                                    </div>
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
