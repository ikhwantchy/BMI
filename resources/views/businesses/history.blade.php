@extends('layouts.app')

@section('title', 'Riwayat Usaha: ' . $business->name)
@section('page-title', 'Riwayat Usaha')

@section('content')
<div class="py-4 space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 pb-4">
        <div class="flex items-center space-x-2 text-xs text-gray-500">
            <a href="{{ route('businesses.index') }}" class="hover:text-emerald-700 transition">Usaha</a>
            <span>/</span>
            <a href="{{ route('businesses.show', $business) }}" class="hover:text-emerald-700 transition">{{ $business->name }}</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">Riwayat</span>
        </div>
        <a href="{{ route('businesses.show', $business) }}" class="btn-secondary text-xs">
            &larr; Kembali ke Detail Usaha
        </a>
    </div>

    {{-- Business Header --}}
    <div class="bg-emerald-900 p-6 text-white border border-emerald-800">
        <p class="text-xs text-emerald-200 font-medium uppercase tracking-wider">{{ $business->business_type }}</p>
        <h2 class="text-lg font-semibold mt-0.5">{{ $business->name }}</h2>
        <p class="text-emerald-100 text-xs mt-1">
            Pemilik:
            <a href="{{ route('members.show', $business->member) }}" class="font-medium underline text-white hover:text-emerald-200">
                {{ $business->member->full_name }}
            </a>
            ({{ $business->member->member_number }})
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Riwayat Kunjungan --}}
        <div class="bg-white border border-gray-200">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="font-semibold text-gray-900 text-xs uppercase tracking-wider">Riwayat Kunjungan</h3>
                    <p class="text-[11px] text-gray-500">Kunjungan lapangan yang tercatat</p>
                </div>
                <a href="{{ route('visits.create', ['business_id' => $business->id]) }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                    + Jadwalkan
                </a>
            </div>

            @if($visits->isEmpty())
                <div class="py-10 text-center text-xs text-gray-400 italic">Belum ada kunjungan lapangan.</div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($visits as $visit)
                        <div class="p-4 hover:bg-gray-50/50 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-xs text-gray-900 font-mono">
                                            {{ $visit->visit_date->translatedFormat('d M Y') }}
                                        </span>
                                        <span class="badge border
                                            {{ $visit->status->value === 'completed' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                                               ($visit->status->value === 'scheduled' ? 'bg-blue-50 border-blue-200 text-blue-800' : 'bg-gray-100 border-gray-200 text-gray-600') }}">
                                            {{ $visit->status->label() }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Periode: <span class="font-medium">{{ $visit->evaluation_period }}</span>
                                        &bull; Petugas: {{ $visit->officer?->name ?? '-' }}
                                    </p>
                                    @if($visit->evaluation)
                                        <p class="text-xs text-emerald-800 mt-1 font-medium">
                                            Skor: {{ number_format($visit->evaluation->final_score, 1) }}
                                            @if($visit->evaluation->recommendation)
                                                &mdash; {{ $visit->evaluation->recommendation->label() }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end gap-1 shrink-0">
                                    <a href="{{ route('visits.show', $visit) }}" class="text-xs font-semibold text-emerald-700 hover:underline whitespace-nowrap">
                                        Detail &rarr;
                                    </a>
                                    @if($visit->evaluation)
                                        <a href="{{ route('evaluations.show', $visit->evaluation) }}" class="text-xs text-gray-500 hover:text-gray-800 whitespace-nowrap">
                                            Evaluasi &rarr;
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($visits->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $visits->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- Riwayat Evaluasi --}}
        <div class="bg-white border border-gray-200">
            <div class="p-4 border-b border-gray-200 bg-gray-50/50">
                <h3 class="font-semibold text-gray-900 text-xs uppercase tracking-wider">Riwayat Evaluasi</h3>
                <p class="text-[11px] text-gray-500">Hasil evaluasi pembinaan usaha berkala</p>
            </div>

            @if($evaluations->isEmpty())
                <div class="py-10 text-center text-xs text-gray-400 italic">Belum ada evaluasi.</div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($evaluations as $evaluation)
                        <div class="p-4 hover:bg-gray-50/50 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-lg font-mono
                                            {{ $evaluation->final_score >= 80 ? 'text-emerald-800' :
                                               ($evaluation->final_score >= 60 ? 'text-amber-800' : 'text-red-800') }}">
                                            {{ number_format($evaluation->final_score, 1) }}
                                        </span>
                                        <span class="text-xs text-gray-400">/ 100</span>
                                        @if($evaluation->recommendation)
                                            <span class="badge border
                                                {{ $evaluation->recommendation->value === 'lanjutkan_tingkatkan' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                                                   ($evaluation->recommendation->value === 'pembinaan_khusus' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-red-50 border-red-200 text-red-800') }}">
                                                {{ $evaluation->recommendation->label() }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Status: <span class="font-medium">{{ $evaluation->status->label() }}</span>
                                        &bull; {{ $evaluation->created_at->translatedFormat('d M Y') }}
                                    </p>
                                    @if($evaluation->submittedBy)
                                        <p class="text-xs text-gray-400 mt-0.5">Diajukan: {{ $evaluation->submittedBy->name }}</p>
                                    @endif
                                    @if($evaluation->validatedBy)
                                        <p class="text-xs text-gray-400">Divalidasi: {{ $evaluation->validatedBy->name }}</p>
                                    @endif
                                </div>
                                <a href="{{ route('evaluations.show', $evaluation) }}" class="text-xs font-semibold text-emerald-700 hover:underline whitespace-nowrap shrink-0">
                                    Lihat &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
