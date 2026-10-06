@extends('layouts.app')

@section('title', 'Detail Anggota: ' . $member->full_name)
@section('page-title', 'Detail Anggota')

@section('content')
<div class="py-4 space-y-6">

    {{-- Breadcrumb & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 pb-4">
        <div class="flex items-center space-x-2 text-xs text-gray-500">
            <a href="{{ route('members.index') }}" class="hover:text-emerald-700 transition">Anggota</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">{{ $member->full_name }}</span>
        </div>
        <div class="flex items-center gap-2">
            @if ($member->latestFinancingAnalysis)
                <a href="{{ route('documents.financing-analysis.show', $member->latestFinancingAnalysis->id) }}" target="_blank" class="btn-secondary text-[#009a4c] border-[#009a4c] font-semibold">
                    Analisis Pembiayaan
                </a>
            @else
                <a href="{{ route('documents.financing-analysis.create', ['member_id' => $member->id]) }}" class="btn-secondary text-xs">
                    + Analisis Pembiayaan
                </a>
            @endif

            @if ($member->latestFeasibilityAssessment)
                <a href="{{ route('documents.feasibility-assessment.show', $member->latestFeasibilityAssessment->id) }}" target="_blank" class="btn-secondary text-teal-700 border-teal-700 font-semibold">
                    Uji Kelayakan
                </a>
            @else
                <a href="{{ route('documents.feasibility-assessment.create', ['member_id' => $member->id]) }}" class="btn-secondary text-xs">
                    + Uji Kelayakan
                </a>
            @endif

            <a href="{{ route('members.history', $member) }}" class="btn-secondary">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat Pembinaan
            </a>
            @can('update', $member)
            <a href="{{ route('members.edit', $member) }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                </svg>
                Edit Anggota
            </a>
            @endcan
        </div>
    </div>

    {{-- Member Profile Card --}}
    <div class="bg-white border border-gray-200">
        <div class="bg-emerald-900 px-6 py-5 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-emerald-800 border border-emerald-700 flex items-center justify-center font-semibold text-xl text-white">
                    {{ strtoupper(substr($member->full_name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-base font-semibold">{{ $member->full_name }}</h3>
                    <p class="text-emerald-200 text-xs font-mono mt-0.5">No. Anggota: {{ $member->member_number }}</p>
                </div>
            </div>
            <div>
                <span class="badge border bg-white/10 border-white/20 text-white">
                    {{ $member->membership_status->label() }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Telepon / WhatsApp</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900 font-mono">{{ $member->phone ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Terdaftar Sejak</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $member->created_at ? $member->created_at->translatedFormat('d F Y') : '-' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Alamat Domisili</dt>
                <dd class="mt-1 text-sm text-gray-800 leading-relaxed">{{ $member->address ?: '-' }}</dd>
            </div>
            @if($member->notes)
            <div class="md:col-span-2">
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Catatan</dt>
                <dd class="mt-1 text-sm text-gray-700 bg-gray-50 p-3 border border-gray-200">{{ $member->notes }}</dd>
            </div>
            @endif
        </div>
    </div>

    {{-- Businesses Section --}}
    <div class="bg-white border border-gray-200 p-6 space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <div>
                <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Daftar Unit Usaha Anggota</h4>
                <p class="text-xs text-gray-500 mt-0.5">Unit usaha yang didaftarkan untuk evaluasi & pembinaan berkala</p>
            </div>
            <a href="{{ route('businesses.create', ['member_id' => $member->id]) }}" class="btn-secondary">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Tambah Usaha
            </a>
        </div>

        @if($member->businesses->isEmpty())
            <div class="text-center py-10 text-gray-400">
                <p class="text-xs text-gray-500">Belum ada data usaha untuk anggota ini.</p>
                <a href="{{ route('businesses.create', ['member_id' => $member->id]) }}" class="mt-2 inline-block text-xs font-semibold text-emerald-700 hover:underline">
                    Daftarkan usaha baru &rarr;
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($member->businesses as $business)
                    <div class="border border-gray-200 p-5 bg-white">
                        <div class="flex items-start justify-between">
                            <div>
                                <h5 class="font-semibold text-gray-900 text-sm">{{ $business->name }}</h5>
                                <span class="badge border border-gray-200 bg-gray-50 text-gray-700 mt-1">
                                    {{ $business->business_type }}
                                </span>
                            </div>
                            <span class="badge border {{ $business->status === 'active' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-gray-100 border-gray-200 text-gray-600' }}">
                                {{ $business->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-gray-600">
                            <div>
                                <span class="text-gray-400 block uppercase tracking-wider text-[10px]">Modal Awal:</span>
                                <span class="font-semibold text-gray-900">Rp {{ number_format($business->initial_capital ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block uppercase tracking-wider text-[10px]">Usia Usaha:</span>
                                <span class="font-medium text-gray-800">{{ $business->business_age_months ? $business->business_age_months . ' Bulan' : '-' }}</span>
                            </div>
                            <div class="col-span-2 mt-1">
                                <span class="text-gray-400 block uppercase tracking-wider text-[10px]">Alamat:</span>
                                <span class="text-gray-800 truncate block">{{ $business->address ?: '-' }}</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                            <span class="text-gray-500">
                                {{ $business->visits->count() }} Kunjungan
                            </span>
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('visits.create', ['business_id' => $business->id]) }}"
                                   class="font-semibold text-emerald-700 hover:text-emerald-900">
                                    + Jadwal Visit
                                </a>
                                <span class="text-gray-200">|</span>
                                <a href="{{ route('businesses.show', $business) }}"
                                   class="font-semibold text-gray-700 hover:text-gray-900 underline">
                                    Detail Usaha &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
