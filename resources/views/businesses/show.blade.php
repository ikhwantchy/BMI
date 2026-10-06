@extends('layouts.app')

@section('title', 'Detail Usaha: ' . $business->name)
@section('page-title', 'Detail Usaha')

@section('content')
<div class="py-4 space-y-6">

    {{-- Breadcrumb & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 pb-4">
        <div class="flex items-center space-x-2 text-xs text-gray-500">
            <a href="{{ route('businesses.index') }}" class="hover:text-emerald-700 transition">Usaha</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">{{ $business->name }}</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('businesses.history', $business) }}" class="btn-secondary">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat Evaluasi
            </a>
            @can('create', App\Models\Visit::class)
            <a href="{{ route('visits.create', ['business_id' => $business->id]) }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Jadwalkan Visit
            </a>
            @endcan
            @if(auth()->user()->isOfficer() || auth()->user()->hasRole('petugas_lapangan'))
                @php 
                    $activeVisit = $business->visits->whereIn('status.value', ['scheduled', 'in_progress', 'needs_revision'])->where('officer_id', auth()->id())->first(); 
                @endphp
                @if($activeVisit)
                    <a href="{{ route('visits.show', $activeVisit) }}" class="btn-primary {{ $activeVisit->status->value === 'needs_revision' ? 'bg-orange-600 hover:bg-orange-700' : ($activeVisit->status->value === 'in_progress' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @if($activeVisit->status->value === 'scheduled')
                            Mulai Kunjungan
                        @elseif($activeVisit->status->value === 'in_progress')
                            Lanjutkan Kunjungan
                        @else
                            Revisi Kunjungan
                        @endif
                    </a>
                @endif
            @endif
            <a href="{{ route('businesses.edit', $business) }}" class="btn-secondary">
                Edit
            </a>
        </div>
    </div>

    {{-- Business Header Card --}}
    <div class="bg-white border border-gray-200">
        <div class="bg-emerald-900 px-6 py-5 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="badge border bg-white/10 border-white/20 text-white mb-2">
                    {{ $business->business_type }}
                </span>
                <h3 class="text-lg font-semibold">{{ $business->name }}</h3>
                <p class="text-emerald-200 text-xs mt-1">
                    Pemilik:
                    <a href="{{ route('members.show', $business->member) }}" class="underline font-medium text-white hover:text-emerald-100">
                        {{ $business->member->full_name }} ({{ $business->member->member_number }})
                    </a>
                </p>
            </div>
            <div>
                <span class="badge border {{ $business->status === 'active' ? 'bg-emerald-800 border-emerald-700 text-white' : 'bg-gray-800 border-gray-700 text-gray-300' }}">
                    Status: {{ $business->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Modal Awal</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900 font-mono">Rp {{ number_format($business->initial_capital ?? 0, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Usia Usaha</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $business->business_age_months ? $business->business_age_months . ' Bulan' : '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Telepon Pemilik</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900 font-mono">{{ $business->member->phone ?: '-' }}</dd>
            </div>
            <div class="md:col-span-3">
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Alamat Usaha</dt>
                <dd class="mt-1 text-sm text-gray-800">{{ $business->address ?: '-' }}</dd>
            </div>
            @if($business->products_services)
                <div class="md:col-span-3">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Produk / Layanan</dt>
                    <dd class="mt-1 text-sm text-gray-700 bg-gray-50 p-3 border border-gray-200">{{ $business->products_services }}</dd>
                </div>
            @endif
            @if($business->initial_condition)
                <div class="md:col-span-3">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kondisi Awal (Baseline)</dt>
                    <dd class="mt-1 text-sm text-gray-700 bg-gray-50 p-3 border border-gray-200">{{ $business->initial_condition }}</dd>
                </div>
            @endif
        </div>
    </div>

    {{-- Kunjungan Lapangan & Riwayat Evaluasi --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Daftar Kunjungan --}}
        <div class="bg-white border border-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Kunjungan Lapangan</h4>
                @can('create', App\Models\Visit::class)
                <a href="{{ route('visits.create', ['business_id' => $business->id]) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                    + Jadwalkan
                </a>
                @endcan
            </div>

            @if($business->visits->isEmpty())
                <div class="text-center py-8 text-gray-400">
                    <p class="text-xs text-gray-500">Belum ada kunjungan lapangan tercatat.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($business->visits as $visit)
                        <div class="py-3 flex items-start justify-between gap-3 text-xs">
                            <div>
                                <span class="font-semibold text-gray-900 font-mono">{{ $visit->visit_date->translatedFormat('d F Y') }}</span>
                                <p class="text-gray-500 mt-0.5 line-clamp-1">{{ $visit->field_notes ?: 'Tidak ada catatan lapangan' }}</p>
                                <span class="text-gray-400 text-[10px]">Oleh: {{ $visit->officer->name }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="badge border {{ $visit->status->value === 'completed' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-amber-50 border-amber-200 text-amber-800' }}">
                                    {{ $visit->status->label() }}
                                </span>
                                <div class="mt-1.5">
                                    <a href="{{ route('visits.show', $visit) }}" class="text-emerald-700 hover:underline font-semibold">
                                        Detail &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Daftar Evaluasi --}}
        <div class="bg-white border border-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Evaluasi Berkala</h4>
                <a href="{{ route('businesses.history', $business) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                    Lihat Histori &rarr;
                </a>
            </div>

            @if($business->evaluations->isEmpty())
                <div class="text-center py-8 text-gray-400">
                    <p class="text-xs text-gray-500">Belum ada evaluasi untuk usaha ini.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($business->evaluations as $eval)
                        <div class="py-3 flex items-center justify-between gap-3 text-xs">
                            <div>
                                <div class="font-semibold text-gray-900">
                                    Periode: {{ $eval->visit?->evaluation_period ?? '-' }}
                                    <span class="text-gray-500 font-normal">({{ $eval->created_at->format('d/m/Y') }})</span>
                                </div>
                                <div class="mt-1">
                                    @if($eval->recommendation)
                                        <span class="badge border
                                            {{ $eval->recommendation->value === 'lanjutkan_tingkatkan' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                                               ($eval->recommendation->value === 'pembinaan_khusus' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-red-50 border-red-200 text-red-800') }}">
                                            {{ $eval->recommendation->label() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold font-mono text-gray-900">
                                    {{ number_format($eval->total_score, 1) }}
                                </div>
                                <a href="{{ route('evaluations.show', $eval) }}" class="text-emerald-700 hover:underline font-semibold mt-1 inline-block">
                                    Detail &rarr;
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
