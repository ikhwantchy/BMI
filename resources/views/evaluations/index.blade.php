@extends('layouts.app')

@section('title', 'Daftar Evaluasi Usaha')
@section('page-title', 'Evaluasi Usaha')

@section('content')
<div class="py-4 space-y-4">

    {{-- Filter & Actions Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="{{ route('evaluations.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2 flex-1">
            <select name="status" class="input-base w-full sm:w-auto">
                <option value="">-- Semua Status Validasi --</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="waiting_validation" {{ request('status') === 'waiting_validation' ? 'selected' : '' }}>Menunggu Validasi</option>
                <option value="validated" {{ request('status') === 'validated' ? 'selected' : '' }}>Tervalidasi</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <select name="recommendation" class="input-base w-full sm:w-auto">
                <option value="">-- Semua Rekomendasi --</option>
                <option value="lanjutkan_tingkatkan" {{ request('recommendation') === 'lanjutkan_tingkatkan' ? 'selected' : '' }}>Lanjutkan & Tingkatkan</option>
                <option value="pembinaan_khusus" {{ request('recommendation') === 'pembinaan_khusus' ? 'selected' : '' }}>Pembinaan Khusus</option>
                <option value="hentikan" {{ request('recommendation') === 'hentikan' ? 'selected' : '' }}>Hentikan Pembiayaan</option>
            </select>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-secondary">
                    Filter
                </button>
                @if(request()->hasAny(['status', 'recommendation']))
                    <a href="{{ route('evaluations.index') }}" class="btn-secondary text-gray-500">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <a href="{{ route('visits.index', ['status' => 'completed']) }}" class="btn-primary w-full sm:w-auto justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Pilih Kunjungan Selesai
        </a>
    </div>

    {{-- Evaluations Table & Mobile Cards --}}
    <div class="bg-white border border-gray-200">
        {{-- Desktop Table (hidden on mobile, unchanged for desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left data-table">
                <thead>
                    <tr>
                        <th>Usaha & Anggota</th>
                        <th>Skor Akhir</th>
                        <th>Rekomendasi</th>
                        <th>Status Alur</th>
                        <th>Waktu Evaluasi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $eval)
                        <tr>
                            <td>
                                <a href="{{ route('evaluations.show', $eval) }}" class="font-medium text-gray-900 hover:text-emerald-700 block">
                                    {{ $eval->business->name }}
                                </a>
                                <p class="text-xs text-gray-500">
                                    {{ $eval->business->member->full_name }} <span class="font-mono text-[11px] text-gray-400">({{ $eval->business->member->member_number }})</span>
                                </p>
                            </td>
                            <td>
                                <div class="flex items-center space-x-1.5 font-mono">
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ number_format($eval->final_score, 1) }}
                                    </span>
                                    <span class="text-[10px] text-gray-400">/ 100</span>
                                </div>
                            </td>
                            <td>
                                @if($eval->recommendation)
                                    <span class="badge border
                                        {{ $eval->recommendation->value === 'lanjutkan_tingkatkan' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                                           ($eval->recommendation->value === 'pembinaan_khusus' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-red-50 border-red-200 text-red-800') }}">
                                        {{ $eval->recommendation->label() }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge border bg-{{ $eval->status->badgeColor() }}-50 border-{{ $eval->status->badgeColor() }}-200 text-{{ $eval->status->badgeColor() }}-800">
                                    {{ $eval->status->label() }}
                                </span>
                            </td>
                            <td class="text-gray-600 text-xs font-mono">
                                {{ $eval->created_at->format('d/m/Y') }}
                            </td>
                            <td class="text-right whitespace-nowrap text-xs">
                                <a href="{{ route('evaluations.show', $eval) }}" class="font-semibold text-emerald-700 hover:text-emerald-900 underline">
                                    Lembar Evaluasi &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400">
                                <p class="text-xs text-gray-500">Belum ada data evaluasi tercatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card List (Khusus Tampilan HP / Petugas Lapangan) --}}
        <div class="block md:hidden divide-y divide-gray-200">
            @forelse($evaluations as $eval)
                <div class="p-4 space-y-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm">
                                <a href="{{ route('evaluations.show', $eval) }}" class="hover:text-emerald-700">
                                    {{ $eval->business->name }}
                                </a>
                            </h3>
                            <p class="text-xs text-gray-600 mt-0.5">
                                {{ $eval->business->member->full_name }}
                                <span class="font-mono text-gray-400 text-[11px]">({{ $eval->business->member->member_number }})</span>
                            </p>
                        </div>
                        <span class="badge border shrink-0 bg-{{ $eval->status->badgeColor() }}-50 border-{{ $eval->status->badgeColor() }}-200 text-{{ $eval->status->badgeColor() }}-800">
                            {{ $eval->status->label() }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs bg-gray-50 p-2.5 border border-gray-100">
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Skor Akhir</span>
                            <div class="flex items-baseline space-x-1 font-mono">
                                <span class="text-base font-bold text-gray-900">{{ number_format($eval->final_score, 1) }}</span>
                                <span class="text-[10px] text-gray-400">/ 100</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Tanggal</span>
                            <span class="font-mono text-gray-700">{{ $eval->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    @if($eval->recommendation)
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider block mb-1">Rekomendasi</span>
                            <span class="badge border
                                {{ $eval->recommendation->value === 'lanjutkan_tingkatkan' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                                   ($eval->recommendation->value === 'pembinaan_khusus' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-red-50 border-red-200 text-red-800') }}">
                                {{ $eval->recommendation->label() }}
                            </span>
                        </div>
                    @endif

                    <div class="flex items-center justify-end pt-2 border-t border-gray-100">
                        <a href="{{ route('evaluations.show', $eval) }}"
                           class="btn-secondary text-xs py-1.5 px-3 font-semibold text-emerald-800 border-emerald-300 hover:bg-emerald-50 w-full text-center justify-center">
                            Buka Lembar Evaluasi &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400">
                    <p class="text-xs text-gray-500">Belum ada data evaluasi tercatat.</p>
                </div>
            @endforelse
        </div>

        @if($evaluations->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $evaluations->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
