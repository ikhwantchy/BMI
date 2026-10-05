@extends('layouts.app')

@section('title', 'Dashboard Manajer')
@section('page-title', 'Ringkasan Eksekutif')

@section('content')
<div class="py-4 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-200 pb-4">
        <div>
            <h2 class="text-base font-semibold text-gray-900 tracking-tight">
                Ringkasan Strategis Pembinaan Usaha
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">Monitoring kinerja dan validasi hasil evaluasi operasional</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 text-[#00a1e8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
                Laporan &amp; Analitik
            </a>
            <a href="{{ route('evaluations.index', ['status' => 'menunggu_validasi']) }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Validasi Evaluasi
            </a>
        </div>
    </div>

    {{-- Stats Grid (Subtle BMI Accent Tops) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white border border-gray-200 border-t-2 border-t-[#009a4c] p-4">
            <div class="flex items-center justify-between text-gray-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Anggota</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
            </div>
            <p class="text-2xl font-semibold text-gray-900 tracking-tight">{{ number_format($totalMembers) }}</p>
            <p class="text-[11px] text-gray-500 mt-1">Anggota aktif terdaftar</p>
        </div>

        <div class="bg-white border border-gray-200 border-t-2 border-t-[#00a1e8] p-4">
            <div class="flex items-center justify-between text-gray-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Usaha Aktif</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25m19.5 0h-4.5M3.75 3h16.5a1.5 1.5 0 011.5 1.5v3.75a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V4.5A1.5 1.5 0 013.75 3z"/>
                </svg>
            </div>
            <p class="text-2xl font-semibold text-gray-900 tracking-tight">{{ number_format($totalBusinesses) }}</p>
            <p class="text-[11px] text-gray-500 mt-1">Unit usaha terdata</p>
        </div>

        <div class="bg-white border border-gray-200 border-t-2 border-t-[#e4c85b] p-4">
            <div class="flex items-center justify-between text-gray-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Menunggu Validasi</span>
                @if($pendingValidations > 0)
                    <span class="px-1.5 py-0.5 text-[10px] font-semibold bg-[#fefce8] text-[#9c7c10] border border-[#e4c85b]">PERLU TINDAKAN</span>
                @endif
            </div>
            <p class="text-2xl font-semibold text-gray-900 tracking-tight">{{ $pendingValidations }}</p>
            <p class="text-[11px] text-gray-500 mt-1">Evaluasi siap disetujui</p>
        </div>

        @php
            $recommended = $evaluationStats['recommended'] ?? 0;
            $notRec      = $evaluationStats['not_recommended'] ?? 0;
            $coaching    = $evaluationStats['continued_coaching'] ?? 0;
            $total       = $recommended + $notRec + $coaching;
        @endphp

        <div class="bg-white border border-gray-200 border-t-2 border-t-red-600 p-4">
            <div class="flex items-center justify-between text-gray-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Perlu Evaluasi Khusus</span>
                <span class="px-1.5 py-0.5 text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200 font-mono">{{ $notRec }}</span>
            </div>
            <p class="text-2xl font-semibold text-gray-900 tracking-tight">{{ $notRec }}</p>
            <p class="text-[11px] text-gray-500 mt-1">Rekomendasi dihentikan</p>
        </div>
    </div>

    {{-- Distribusi Rekomendasi Section --}}
    @if($total > 0)
    <div class="bg-white border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Distribusi Rekomendasi Hasil Pembinaan</h3>
            <span class="text-xs text-gray-500 font-mono">Total: {{ $total }} Evaluasi Selesai</span>
        </div>

        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="font-medium text-[#006331]">Lanjutkan &amp; Tingkatkan (Skor &ge; 80)</span>
                    <span class="font-semibold text-gray-900 font-mono">{{ $recommended }} ({{ round(($recommended/$total)*100) }}%)</span>
                </div>
                <div class="h-1.5 bg-gray-100 overflow-hidden">
                    <div class="h-full bg-[#009a4c] transition-all duration-300" style="width: {{ ($recommended/$total)*100 }}%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="font-medium text-[#9c7c10]">Pembinaan Khusus (Skor 60 - 79)</span>
                    <span class="font-semibold text-gray-900 font-mono">{{ $coaching }} ({{ round(($coaching/$total)*100) }}%)</span>
                </div>
                <div class="h-1.5 bg-gray-100 overflow-hidden">
                    <div class="h-full bg-[#e4c85b] transition-all duration-300" style="width: {{ ($coaching/$total)*100 }}%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="font-medium text-red-800">Hentikan / Tidak Layak (Skor &lt; 60)</span>
                    <span class="font-semibold text-gray-900 font-mono">{{ $notRec }} ({{ round(($notRec/$total)*100) }}%)</span>
                </div>
                <div class="h-1.5 bg-gray-100 overflow-hidden">
                    <div class="h-full bg-red-600 transition-all duration-300" style="width: {{ ($notRec/$total)*100 }}%"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Recent Evaluations Table --}}
    <div class="bg-white border border-gray-200">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50/50">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
                <h3 class="text-xs font-semibold text-gray-800 uppercase tracking-wider">Evaluasi Terbaru</h3>
            </div>
            <a href="{{ route('evaluations.index') }}" class="text-xs font-semibold text-[#009a4c] hover:text-[#007d3e]">
                Semua Evaluasi &rarr;
            </a>
        </div>

        @if($recentEvaluations->isEmpty())
            <div class="text-center py-12 text-gray-400">
                <p class="text-xs text-gray-500">Belum ada catatan evaluasi terbaru.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left data-table">
                    <thead>
                        <tr>
                            <th>Anggota</th>
                            <th>Usaha</th>
                            <th>Skor</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEvaluations as $eval)
                            <tr>
                                <td class="font-medium text-gray-900">
                                    {{ $eval->business->member->full_name ?? '-' }}
                                    <div class="text-xs text-gray-400 font-mono">{{ $eval->business->member->member_number ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="text-gray-800 font-medium">{{ $eval->business->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $eval->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    @if($eval->total_score)
                                        <span class="font-mono text-sm font-semibold text-gray-900">{{ number_format($eval->total_score, 1) }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge border bg-{{ $eval->status->badgeColor() }}-50 border-{{ $eval->status->badgeColor() }}-200 text-{{ $eval->status->badgeColor() }}-800">
                                        {{ $eval->status->label() }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('evaluations.show', $eval) }}"
                                       class="text-xs font-semibold text-[#009a4c] hover:text-[#007d3e] underline">
                                        Detail &rarr;
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
