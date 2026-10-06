@extends('layouts.app')

@section('title', 'Dokumen Operasional Klien')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Dokumen Operasional Klien</h1>
            <p class="text-xs text-gray-500 mt-1">
                Pusat penerbitan & cetak dokumen resmi Koperasi Syariah BMI: Analisis Pembiayaan, Uji Kelayakan, dan Evaluasi Usaha.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('documents.financing-analysis.create') }}"
               class="px-3.5 py-2 bg-[#009a4c] hover:bg-[#007d3e] text-white text-xs font-semibold uppercase tracking-wider rounded transition-colors inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                + Analisis Pembiayaan
            </a>
            <a href="{{ route('documents.feasibility-assessment.create') }}"
               class="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold uppercase tracking-wider rounded transition-colors inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                + Uji Kelayakan
            </a>
        </div>
    </div>

    {{-- 3 Kategori Dokumen Resmi Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Card 1: Analisis Pembiayaan --}}
        <div class="bg-white border border-gray-200 p-5 rounded-lg shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Dokumen 1
                    </span>
                    <svg class="w-5 h-5 text-[#009a4c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.5L19 7.5V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-gray-900 uppercase">Analisis Pembiayaan</h2>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                    Menyajikan Bagian A–F: Data Anggota, Pembiayaan, Kapasitas Usaha, Aset/Simpanan, Arus Kas, dan Kemampuan Mengangsur (Saving Capacity).
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400 font-mono">{{ $recentAnalyses->count() }} Data Tersedia</span>
                <a href="{{ route('documents.financing-analysis.create') }}" class="text-xs font-bold text-[#009a4c] hover:underline">
                    Buat Baru &rarr;
                </a>
            </div>
        </div>

        {{-- Card 2: Uji Kelayakan --}}
        <div class="bg-white border border-gray-200 p-5 rounded-lg shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider rounded bg-teal-50 text-teal-700 border border-teal-200">
                        Dokumen 2
                    </span>
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-gray-900 uppercase">Uji Kelayakan</h2>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                    Menyajikan Bagian A–F: Data Pribadi & NIK, Data Pasangan, Anggota Keluarga & Rumah Tinggal, Aset Rumah Tangga, Karakter & Rembug Pusat.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400 font-mono">{{ $recentAssessments->count() }} Data Tersedia</span>
                <a href="{{ route('documents.feasibility-assessment.create') }}" class="text-xs font-bold text-teal-700 hover:underline">
                    Buat Baru &rarr;
                </a>
            </div>
        </div>

        {{-- Card 3: Evaluasi & Pembinaan --}}
        <div class="bg-white border border-gray-200 p-5 rounded-lg shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider rounded bg-amber-50 text-amber-700 border border-amber-200">
                        Dokumen 3
                    </span>
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-gray-900 uppercase">Lembar Evaluasi Usaha</h2>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                    Menyajikan hasil monitoring berkala, 5 Pilar Observasi Lapangan, Pembobotan Skor 100%, Dokumentasi Foto, dan Tindak Lanjut Pembinaan.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400 font-mono">{{ $recentEvaluations->count() }} Tervalidasi</span>
                <a href="{{ route('evaluations.index') }}" class="text-xs font-bold text-amber-700 hover:underline">
                    Lihat Evaluasi &rarr;
                </a>
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat Dokumen Yang Digenerate (Traceability) --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">
                    Daftar Dokumen Resmi Siap Cetak & PDF
                </h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Semua dokumen menggunakan format dan kalkulasi asli formulir BMI.</p>
            </div>
            {{-- Filter Form --}}
            <form method="GET" action="{{ route('documents.index') }}" class="flex items-center gap-2">
                <select name="type" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded px-2.5 py-1.5 bg-white text-gray-700">
                    <option value="">Semua Jenis Dokumen</option>
                    <option value="financing_analysis" {{ request('type') == 'financing_analysis' ? 'selected' : '' }}>Analisis Pembiayaan</option>
                    <option value="feasibility_assessment" {{ request('type') == 'feasibility_assessment' ? 'selected' : '' }}>Uji Kelayakan</option>
                    <option value="business_evaluation" {{ request('type') == 'business_evaluation' ? 'selected' : '' }}>Evaluasi Usaha</option>
                </select>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/no anggota..."
                       class="text-xs border border-gray-300 rounded px-2.5 py-1.5 bg-white text-gray-700">
                <button type="submit" class="text-xs px-3 py-1.5 bg-gray-800 text-white font-medium rounded hover:bg-black">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-gray-100 text-gray-700 uppercase font-semibold text-[10px] tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="p-3">Jenis Dokumen</th>
                        <th class="p-3">No. Dokumen</th>
                        <th class="p-3">Nama Anggota</th>
                        <th class="p-3">Unit Usaha</th>
                        <th class="p-3">Dibuat Oleh</th>
                        <th class="p-3">Waktu Terbit</th>
                        <th class="p-3 text-right">Aksi Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 font-normal text-gray-800">
                    @forelse ($histories as $doc)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-3">
                                @if ($doc->document_type === 'financing_analysis')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Analisis Pembiayaan
                                    </span>
                                @elseif ($doc->document_type === 'feasibility_assessment')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800">
                                        Uji Kelayakan
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Evaluasi Usaha
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 font-mono font-semibold text-gray-900">
                                {{ $doc->document_number }}
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-gray-900">{{ $doc->member?->full_name }}</div>
                                <div class="text-[10px] text-gray-500 font-mono">{{ $doc->member?->member_number }}</div>
                            </td>
                            <td class="p-3">
                                {{ $doc->business?->name ?? '-' }}
                            </td>
                            <td class="p-3 text-gray-600">
                                {{ $doc->generatedBy?->name ?? 'Sistem' }}
                            </td>
                            <td class="p-3 text-gray-500">
                                {{ $doc->created_at?->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-3 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    @if ($doc->document_type === 'financing_analysis')
                                        <a href="{{ route('documents.financing-analysis.show', $doc->reference_id) }}"
                                           target="_blank"
                                           class="px-2 py-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded text-[11px]">
                                            Cetak / Preview
                                        </a>
                                        <a href="{{ route('documents.financing-analysis.pdf', $doc->reference_id) }}"
                                           class="px-2 py-1 bg-[#009a4c] hover:bg-[#007d3e] text-white font-medium rounded text-[11px]">
                                            PDF
                                        </a>
                                    @elseif ($doc->document_type === 'feasibility_assessment')
                                        <a href="{{ route('documents.feasibility-assessment.show', $doc->reference_id) }}"
                                           target="_blank"
                                           class="px-2 py-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded text-[11px]">
                                            Cetak / Preview
                                        </a>
                                        <a href="{{ route('documents.feasibility-assessment.pdf', $doc->reference_id) }}"
                                           class="px-2 py-1 bg-teal-700 hover:bg-teal-800 text-white font-medium rounded text-[11px]">
                                            PDF
                                        </a>
                                    @else
                                        <a href="{{ route('documents.business-evaluation.show', $doc->reference_id) }}"
                                           target="_blank"
                                           class="px-2 py-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded text-[11px]">
                                            Cetak / Preview
                                        </a>
                                        <a href="{{ route('documents.business-evaluation.pdf', $doc->reference_id) }}"
                                           class="px-2 py-1 bg-amber-700 hover:bg-amber-800 text-white font-medium rounded text-[11px]">
                                            PDF
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">
                                Belum ada riwayat penerbitan dokumen. Pilih menu di atas untuk membuat dokumen baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($histories->hasPages())
        <div class="p-3 border-t border-gray-200">
            {{ $histories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
