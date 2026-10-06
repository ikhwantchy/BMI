@extends('layouts.app')

@section('title', 'Detail Kunjungan: ' . $visit->business->name)
@section('page-title', 'Detail Kunjungan Lapangan')

@section('content')

@if((auth()->user()->isOfficer() || auth()->user()->hasRole('petugas_lapangan')) && in_array($visit->status->value, ['scheduled', 'in_progress', 'needs_revision']))
    @include('visits.execute-form')
@else

<div class="py-4 space-y-6">

    {{-- Breadcrumb & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 pb-4">
        <div class="flex items-center space-x-2 text-xs text-gray-500">
            <a href="{{ route('visits.index') }}" class="hover:text-emerald-700 transition">Kunjungan</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">{{ $visit->business->name }}</span>
            <span>/</span>
            <span class="text-gray-500">{{ $visit->visit_date->translatedFormat('d M Y') }}</span>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($visit->status->value === 'scheduled')
                <form action="{{ route('visits.complete', $visit) }}" method="POST" onsubmit="return confirm('Tandai kunjungan ini sebagai selesai?')">
                    @csrf
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                        Tandai Selesai
                    </button>
                </form>
                <a href="{{ route('visits.edit', $visit) }}" class="btn-secondary">
                    Edit
                </a>
            @endif

            @if($visit->status->value === 'completed')
                @if(!$visit->evaluation)
                    <a href="{{ route('evaluations.create', $visit) }}" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Input Form Evaluasi & Skor
                    </a>
                @else
                    <a href="{{ route('evaluations.show', $visit->evaluation) }}" class="btn-secondary">
                        Lihat Hasil Evaluasi &rarr;
                    </a>
                @endif
            @endif
        </div>
    </div>

    {{-- Visit Info Card --}}
    <div class="bg-white border border-gray-200 p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-200 gap-3">
            <div>
                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Unit Usaha Binaan</span>
                <h3 class="text-base font-semibold text-gray-900 mt-0.5">
                    <a href="{{ route('businesses.show', $visit->business) }}" class="hover:text-emerald-700">
                        {{ $visit->business->name }}
                    </a>
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Anggota: <span class="font-medium text-gray-800">{{ $visit->business->member->full_name }}</span> ({{ $visit->business->member->member_number }})
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="badge border bg-{{ $visit->status->badgeColor() }}-50 border-{{ $visit->status->badgeColor() }}-200 text-{{ $visit->status->badgeColor() }}-800">
                    {{ $visit->status->label() }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Kunjungan</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900 font-mono">{{ $visit->visit_date->translatedFormat('l, d F Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Periode Evaluasi</dt>
                <dd class="mt-1 text-sm font-semibold font-mono text-gray-900">{{ $visit->evaluation_period }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Petugas Pendamping</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $visit->officer?->name ?: '-' }}</dd>
            </div>
            <div class="md:col-span-3">
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Catatan Lapangan & Temuan</dt>
                <dd class="mt-1 text-sm text-gray-800 bg-gray-50 p-4 border border-gray-200 whitespace-pre-line leading-relaxed">
                    {{ $visit->field_notes ?: 'Tidak ada catatan khusus yang dicantumkan.' }}
                </dd>
            </div>
        </div>
    </div>

    {{-- Evaluasi Section if exists --}}
    @if($visit->evaluation)
        <div class="bg-gray-50 border border-emerald-300 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-800">Hasil Evaluasi Lapangan</span>
                <div class="flex items-center space-x-3 mt-1">
                    <span class="text-xl font-semibold font-mono text-emerald-950">Skor: {{ number_format($visit->evaluation->final_score, 1) }}</span>
                    @if($visit->evaluation->recommendation)
                        <span class="badge border bg-emerald-100 border-emerald-300 text-emerald-900">
                            {{ $visit->evaluation->recommendation->label() }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-600 mt-1">Status Validasi: <span class="font-semibold">{{ $visit->evaluation->status->label() }}</span></p>
            </div>
            <a href="{{ route('evaluations.show', $visit->evaluation) }}" class="btn-primary self-start sm:self-center">
                Buka Lembar Evaluasi &rarr;
            </a>
        </div>
    @endif

    {{-- Dokumentasi & Foto Lapangan --}}
    <div class="bg-white border border-gray-200 p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-200 gap-3">
            <div>
                <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Dokumentasi & Bukti Foto Lapangan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Unggah foto kondisi tempat usaha, display produk, atau berkas pendukung</p>
            </div>
            <span class="text-xs text-gray-400 font-mono">{{ $visit->documents->count() }} Berkas</span>
        </div>

        @include('visits.partials.upload-form')
    </div>

</div>

@endif
@endsection
