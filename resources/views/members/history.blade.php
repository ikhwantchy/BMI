@extends('layouts.app')

@section('title', 'Riwayat Pembinaan: ' . $member->full_name)
@section('page-title', 'Riwayat Pembinaan & Evaluasi')

@section('content')
<div class="py-4 space-y-6">

    {{-- Breadcrumb & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('members.index') }}" class="hover:text-emerald-700">Anggota</a>
                <span>/</span>
                <a href="{{ route('members.show', $member) }}" class="hover:text-emerald-700">{{ $member->full_name }}</a>
                <span>/</span>
                <span class="text-gray-900 font-semibold">Riwayat</span>
            </div>
            <h3 class="text-base font-semibold text-gray-900">Riwayat Pembinaan & Rekam Evaluasi</h3>
            <p class="text-xs text-gray-500 mt-0.5">Histori longitudinal perkembangan usaha dan evaluasi berkala anggota</p>
        </div>
        <a href="{{ route('members.show', $member) }}" class="btn-secondary text-xs">
            &larr; Kembali ke Profil
        </a>
    </div>

    {{-- Timeline per Usaha --}}
    @forelse($member->businesses as $business)
        <div class="bg-white border border-gray-200 p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-200 gap-2">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                        <span>{{ $business->name }}</span>
                        <span class="badge border border-gray-200 bg-gray-50 text-gray-700">
                            {{ $business->business_type }}
                        </span>
                    </h4>
                    <p class="text-xs text-gray-500 mt-0.5">Modal Awal: Rp {{ number_format($business->initial_capital ?? 0, 0, ',', '.') }} &bull; Usia: {{ $business->business_age_months ?? 0 }} Bulan</p>
                </div>
                <a href="{{ route('businesses.show', $business) }}" class="btn-secondary text-xs">
                    Lihat Unit Usaha &rarr;
                </a>
            </div>

            {{-- Evaluations Timeline --}}
            <div class="space-y-4">
                <h5 class="text-xs font-semibold uppercase tracking-wider text-gray-600">Riwayat Evaluasi Usaha</h5>
                @if($business->evaluations->isEmpty())
                    <p class="text-xs text-gray-400 italic">Belum ada evaluasi yang tercatat untuk usaha ini.</p>
                @else
                    <div class="border-l-2 border-emerald-600 pl-4 space-y-4 ml-1">
                        @foreach($business->evaluations as $eval)
                            <div class="bg-gray-50 border border-gray-200 p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-sm font-semibold text-gray-900">
                                            Skor: {{ number_format($eval->final_score, 1) }}
                                        </span>
                                        @if($eval->recommendation)
                                            <span class="badge border
                                                {{ $eval->recommendation->value === 'lanjutkan_tingkatkan' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                                                   ($eval->recommendation->value === 'pembinaan_khusus' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-red-50 border-red-200 text-red-800') }}">
                                                {{ $eval->recommendation->label() }}
                                            </span>
                                        @endif
                                        <span class="badge border border-gray-300 bg-white text-gray-700">
                                            {{ $eval->status->label() }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 font-mono">
                                        Periode: {{ $eval->visit?->evaluation_period ?? '-' }} ({{ $eval->created_at->format('d/m/Y') }})
                                    </div>
                                </div>

                                @if($eval->details->isNotEmpty())
                                    <div class="mt-3 pt-3 border-t border-gray-200 grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                                        @foreach($eval->details as $detail)
                                            <div class="bg-white p-2 border border-gray-200">
                                                <span class="text-gray-400 block truncate text-[11px]">{{ $detail->parameter?->name }}</span>
                                                <span class="font-semibold text-gray-900">{{ $detail->score }}</span>
                                                <span class="text-gray-400 text-[10px]"> (Bobot: {{ $detail->parameter?->weight_percent }}%)</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mt-3 flex items-center justify-between text-xs text-gray-500 pt-2 border-t border-gray-200">
                                    <span>Petugas: {{ $eval->submittedBy?->name ?? 'Belum disubmit' }}</span>
                                    <a href="{{ route('evaluations.show', $eval) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 underline">
                                        Rincian Evaluasi &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Field Visits --}}
            <div class="space-y-3 pt-3 border-t border-gray-200">
                <h5 class="text-xs font-semibold uppercase tracking-wider text-gray-600">Kunjungan Lapangan Terakhir</h5>
                @if($business->visits->isEmpty())
                    <p class="text-xs text-gray-400 italic">Belum ada kunjungan lapangan tercatat.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($business->visits->take(6) as $visit)
                            <div class="p-3 border border-gray-200 bg-white text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-800 font-mono">{{ $visit->visit_date->translatedFormat('d M Y') }}</span>
                                    <span class="badge border {{ $visit->status->value === 'completed' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-amber-50 border-amber-200 text-amber-800' }}">
                                        {{ $visit->status->label() }}
                                    </span>
                                </div>
                                <p class="text-gray-500 text-[11px] truncate">Catatan: {{ $visit->field_notes ?: 'Tidak ada catatan' }}</p>
                                <div class="pt-1 text-right">
                                    <a href="{{ route('visits.show', $visit) }}" class="text-emerald-700 hover:underline text-[11px] font-semibold">Lihat Detail &rarr;</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white border border-gray-200 p-12 text-center">
            <p class="text-xs text-gray-500">Anggota ini belum memiliki data usaha dan riwayat evaluasi.</p>
            <div class="mt-4">
                <a href="{{ route('businesses.create', ['member_id' => $member->id]) }}" class="btn-primary">
                    + Daftarkan Usaha Pertama
                </a>
            </div>
        </div>
    @endforelse

</div>
@endsection
