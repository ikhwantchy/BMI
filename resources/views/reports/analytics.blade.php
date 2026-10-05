@extends('layouts.app')

@section('title', 'Analitik Komparasi Cabang')
@section('page-title', 'Analitik & Komparasi Cabang')

@section('content')
<div class="py-4 space-y-6">

    {{-- Header & Period Filter --}}
    <div class="border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900 tracking-tight">Analitik Komparasi Kinerja Cabang</h2>
            <p class="text-xs text-gray-500 mt-0.5">Analisis skor rata-rata parameter evaluasi 5 pilar dan pencapaian rekomendasi antar cabang</p>
        </div>
        <form method="GET" action="{{ route('reports.analytics') }}" class="flex items-center gap-2">
            <input type="month" name="period" value="{{ $period }}"
                   class="text-xs border border-gray-300 px-3 py-1.5 focus:border-[#006633] focus:ring-0">
            <button type="submit"
                    class="px-3 py-1.5 bg-[#006633] text-white text-xs font-semibold hover:bg-[#00552b] transition-colors">
                Filter
            </button>
            @if($period)
                <a href="{{ route('reports.analytics') }}"
                   class="px-3 py-1.5 border border-gray-300 text-xs text-gray-600 hover:bg-gray-100">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Parameter Weighting & Average Performance (5 Pilar) --}}
    <div class="bg-white border border-gray-200 p-5 space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <div>
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Kinerja 5 Pilar Evaluasi Usaha</h3>
                <p class="text-[11px] text-gray-500">Skor rata-rata berdasarkan bobot resmi Kopsyah BMI</p>
            </div>
            <div class="text-right">
                <span class="text-[10px] uppercase font-bold text-gray-400 block">Rata-rata Skor Komposit</span>
                <span class="text-xl font-bold font-mono text-[#006633]">{{ $paramAverages['overall_avg'] }} <span class="text-xs font-normal text-gray-400">/ 100</span></span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            {{-- Kondisi Usaha 20% --}}
            <div class="bg-gray-50 border border-gray-200 p-3">
                <div class="flex items-center justify-between text-[11px] text-gray-600 mb-1">
                    <span class="font-semibold">Kondisi Usaha</span>
                    <span class="font-mono text-emerald-800 font-bold">20%</span>
                </div>
                <div class="text-lg font-bold font-mono text-gray-900 mb-1.5">{{ $paramAverages['kondisi_usaha'] }}</div>
                <div class="w-full bg-gray-200 h-1.5">
                    <div class="bg-[#009a4c] h-1.5" style="width: {{ min(100, $paramAverages['kondisi_usaha']) }}%"></div>
                </div>
            </div>

            {{-- Perkembangan Omzet 25% --}}
            <div class="bg-gray-50 border border-gray-200 p-3">
                <div class="flex items-center justify-between text-[11px] text-gray-600 mb-1">
                    <span class="font-semibold">Omzet Usaha</span>
                    <span class="font-mono text-emerald-800 font-bold">25%</span>
                </div>
                <div class="text-lg font-bold font-mono text-gray-900 mb-1.5">{{ $paramAverages['perkembangan_omzet'] }}</div>
                <div class="w-full bg-gray-200 h-1.5">
                    <div class="bg-[#009a4c] h-1.5" style="width: {{ min(100, $paramAverages['perkembangan_omzet']) }}%"></div>
                </div>
            </div>

            {{-- Aktivitas Usaha 20% --}}
            <div class="bg-gray-50 border border-gray-200 p-3">
                <div class="flex items-center justify-between text-[11px] text-gray-600 mb-1">
                    <span class="font-semibold">Aktivitas Usaha</span>
                    <span class="font-mono text-emerald-800 font-bold">20%</span>
                </div>
                <div class="text-lg font-bold font-mono text-gray-900 mb-1.5">{{ $paramAverages['aktivitas_usaha'] }}</div>
                <div class="w-full bg-gray-200 h-1.5">
                    <div class="bg-[#009a4c] h-1.5" style="width: {{ min(100, $paramAverages['aktivitas_usaha']) }}%"></div>
                </div>
            </div>

            {{-- Pengelolaan Keuangan 20% --}}
            <div class="bg-gray-50 border border-gray-200 p-3">
                <div class="flex items-center justify-between text-[11px] text-gray-600 mb-1">
                    <span class="font-semibold">Pengelolaan Keuangan</span>
                    <span class="font-mono text-emerald-800 font-bold">20%</span>
                </div>
                <div class="text-lg font-bold font-mono text-gray-900 mb-1.5">{{ $paramAverages['pengelolaan_keuangan'] }}</div>
                <div class="w-full bg-gray-200 h-1.5">
                    <div class="bg-[#009a4c] h-1.5" style="width: {{ min(100, $paramAverages['pengelolaan_keuangan']) }}%"></div>
                </div>
            </div>

            {{-- Kendala Usaha 15% --}}
            <div class="bg-gray-50 border border-gray-200 p-3">
                <div class="flex items-center justify-between text-[11px] text-gray-600 mb-1">
                    <span class="font-semibold">Mitigasi Kendala</span>
                    <span class="font-mono text-emerald-800 font-bold">15%</span>
                </div>
                <div class="text-lg font-bold font-mono text-gray-900 mb-1.5">{{ $paramAverages['kendala_usaha'] }}</div>
                <div class="w-full bg-gray-200 h-1.5">
                    <div class="bg-[#009a4c] h-1.5" style="width: {{ min(100, $paramAverages['kendala_usaha']) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Branch Comparison Matrix Table --}}
    <div class="bg-white border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Matriks Komparasi Antar Kantor Cabang</h3>
            <span class="text-[11px] text-gray-500 font-mono">{{ $branchStats->count() }} Cabang Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-[10px] tracking-wider border-b border-gray-200 font-semibold">
                    <tr>
                        <th class="px-4 py-3">Kode & Nama Cabang</th>
                        <th class="px-4 py-3 text-center">Anggota / Usaha</th>
                        <th class="px-4 py-3 text-center">Total Evaluasi</th>
                        <th class="px-4 py-3 text-center">Skor Rata-Rata</th>
                        <th class="px-4 py-3 text-center">Direkomendasikan (≥80)</th>
                        <th class="px-4 py-3 text-center">Pembinaan (60-79)</th>
                        <th class="px-4 py-3 text-center">Tidak Rekomendasi (&lt;60)</th>
                        <th class="px-4 py-3 text-right">Rasio Kelayakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($branchStats as $bs)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-[11px] px-1.5 py-0.5 bg-gray-100 font-bold text-gray-700">{{ $bs['branch']->code }}</span>
                                    <span>{{ $bs['branch']->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-gray-600">
                                {{ $bs['branch']->members_count }} / {{ $bs['branch']->businesses_count }}
                            </td>
                            <td class="px-4 py-3 text-center font-mono font-bold text-gray-800">
                                {{ $bs['total_evaluations'] }}
                            </td>
                            <td class="px-4 py-3 text-center font-mono font-bold">
                                <span class="px-2 py-0.5 {{ $bs['avg_score'] >= 80 ? 'bg-emerald-100 text-emerald-800' : ($bs['avg_score'] >= 60 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $bs['avg_score'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-emerald-700 font-semibold">
                                {{ $bs['recommended_count'] }}
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-amber-700 font-semibold">
                                {{ $bs['coaching_count'] }}
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-rose-700 font-semibold">
                                {{ $bs['not_recommended_cnt'] }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-[#006633]">
                                {{ $bs['recommendation_rate'] }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 italic">
                                Belum ada data cabang yang dapat dikomparasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Business Types --}}
    <div class="bg-white border border-gray-200 p-5">
        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3">Distribusi Sektor Usaha Terbanyak</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
            @forelse($sectorStats as $sector)
                <div class="bg-gray-50 border border-gray-200 p-3 text-center">
                    <span class="text-xs font-semibold text-gray-800 block truncate" title="{{ $sector->business_type }}">{{ $sector->business_type }}</span>
                    <span class="text-lg font-bold font-mono text-gray-900 mt-1 block">{{ $sector->total }}</span>
                    <span class="text-[10px] text-gray-400">Unit Usaha</span>
                </div>
            @empty
                <div class="col-span-6 text-center text-gray-400 text-xs italic py-4">Belum ada data sektor usaha.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
