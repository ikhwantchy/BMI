@extends('layouts.app')

@section('title', 'Data Usaha Anggota')
@section('page-title', 'Usaha Anggota')

@section('content')
<div class="py-4 space-y-4">

    {{-- Filter & Actions Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="{{ route('businesses.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2 flex-1">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari nama usaha, jenis, atau pemilik..."
                   class="input-base w-full sm:max-w-sm">
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-secondary">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'member_id']))
                    <a href="{{ route('businesses.index') }}" class="btn-secondary text-gray-500">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <a href="{{ route('businesses.create') }}" class="btn-primary w-full sm:w-auto justify-center shrink-0 drawer-link">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Tambah Usaha
        </a>
    </div>

    {{-- Business List Table & Mobile Cards --}}
    <div class="bg-white border border-gray-200">
        {{-- Desktop Table (hidden on mobile, unchanged for desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left data-table">
                <thead>
                    <tr>
                        <th>Nama Usaha</th>
                        <th>Pemilik (Anggota)</th>
                        <th>Jenis Usaha</th>
                        <th>Modal Awal</th>
                        <th>Usia Usaha</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($businesses as $business)
                        <tr>
                            <td>
                                <a href="{{ route('businesses.show', $business) }}" class="font-medium text-gray-900 hover:text-emerald-700">
                                    {{ $business->name }}
                                </a>
                                <p class="text-xs text-gray-400 truncate max-w-xs">{{ $business->address ?: '-' }}</p>
                            </td>
                            <td>
                                <a href="{{ route('members.show', $business->member) }}" class="text-emerald-800 font-medium hover:underline">
                                    {{ $business->member->full_name }}
                                </a>
                                <p class="text-xs text-gray-400 font-mono">{{ $business->member->member_number }}</p>
                            </td>
                            <td>
                                <span class="badge border border-gray-200 bg-gray-50 text-gray-700">
                                    {{ $business->business_type }}
                                </span>
                            </td>
                            <td class="text-gray-900 font-semibold text-xs font-mono">
                                Rp {{ number_format($business->initial_capital ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-gray-600 text-xs">
                                {{ $business->business_age_months ? $business->business_age_months . ' bln' : '-' }}
                            </td>
                            <td>
                                <span class="badge border {{ $business->status === 'active' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-gray-100 border-gray-200 text-gray-600' }}">
                                    {{ $business->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3 text-xs">
                                    <a href="{{ route('businesses.show', $business) }}" class="font-semibold text-emerald-700 hover:text-emerald-900 underline">
                                        Detail
                                    </a>
                                    <a href="{{ route('businesses.edit', $business) }}" class="font-semibold text-gray-600 hover:text-gray-900 drawer-link">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400">
                                <p class="text-xs text-gray-500">Belum ada unit usaha terdaftar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card List (Khusus Tampilan HP / Petugas Lapangan) --}}
        <div class="block md:hidden divide-y divide-gray-200">
            @forelse($businesses as $business)
                <div class="p-4 space-y-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm">
                                <a href="{{ route('businesses.show', $business) }}" class="hover:text-emerald-700">
                                    {{ $business->name }}
                                </a>
                            </h3>
                            <p class="text-xs text-emerald-800 font-medium mt-0.5">
                                {{ $business->member->full_name }}
                                <span class="font-mono text-gray-400 text-[11px]">({{ $business->member->member_number }})</span>
                            </p>
                        </div>
                        <span class="badge border shrink-0 {{ $business->status === 'active' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-gray-100 border-gray-200 text-gray-600' }}">
                            {{ $business->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs bg-gray-50 p-2.5 border border-gray-100">
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Jenis Usaha</span>
                            <span class="font-medium text-gray-800">{{ $business->business_type }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Modal Awal</span>
                            <span class="font-mono font-semibold text-gray-900">Rp {{ number_format($business->initial_capital ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if($business->address)
                        <div class="flex items-start text-xs text-gray-500">
                            <svg class="w-3.5 h-3.5 mr-1.5 mt-0.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="line-clamp-2 leading-relaxed">{{ $business->address }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <a href="{{ route('businesses.show', $business) }}"
                           class="btn-secondary text-xs py-1.5 px-3 font-semibold text-emerald-800 border-emerald-300 hover:bg-emerald-50">
                            Detail Usaha
                        </a>
                        <a href="{{ route('businesses.edit', $business) }}"
                           class="btn-secondary text-xs py-1.5 px-3 drawer-link">
                            Edit
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400">
                    <p class="text-xs text-gray-500">Belum ada unit usaha terdaftar.</p>
                </div>
            @endforelse
        </div>

        @if($businesses->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $businesses->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
