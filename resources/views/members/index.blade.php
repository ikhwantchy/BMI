@extends('layouts.app')

@section('title', 'Data Anggota')
@section('page-title', 'Data Anggota')

@section('content')
<div class="py-4 space-y-4">

    {{-- Filter & Actions Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="{{ route('members.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2 flex-1">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari nama, nomor anggota, atau telepon..."
                   class="input-base w-full sm:max-w-sm">
            <div class="flex items-center gap-2">
                <select name="status" class="input-base flex-1 sm:w-auto">
                    <option value="">Semua Status</option>
                    <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive"  {{ request('status') === 'inactive'  ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                </select>
                <button type="submit" class="btn-secondary shrink-0">
                    Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('members.index') }}" class="btn-secondary text-gray-500 shrink-0">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        @can('create', App\Models\Member::class)
            <a href="{{ route('members.create') }}" class="btn-primary w-full sm:w-auto justify-center shrink-0 drawer-link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Tambah Anggota
            </a>
        @endcan
    </div>

    {{-- Table Container --}}
    <div class="bg-white border border-gray-200">
        @if($members->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
                <p class="text-xs text-gray-500">Tidak ada data anggota ditemukan.</p>
                @can('create', App\Models\Member::class)
                    <a href="{{ route('members.create') }}" class="mt-2 inline-block text-xs font-semibold text-emerald-700 hover:underline">
                        Daftarkan Anggota Baru &rarr;
                    </a>
                @endcan
            </div>
        @else
            {{-- Desktop Table Container (hidden on mobile, unchanged for desktop) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left data-table">
                    <thead>
                        <tr>
                            <th>No. Anggota</th>
                            <th>Nama Lengkap</th>
                            <th>Telepon</th>
                            <th>Status Keanggotaan</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                            <tr>
                                <td class="font-mono text-xs text-gray-600 font-semibold">{{ $member->member_number }}</td>
                                <td>
                                    <div class="font-medium text-gray-900">{{ $member->full_name }}</div>
                                    <div class="text-xs text-gray-400 truncate max-w-sm">{{ $member->address }}</div>
                                </td>
                                <td class="text-gray-600 font-mono text-xs">{{ $member->phone ?? '-' }}</td>
                                <td>
                                    <span class="badge border bg-{{ $member->membership_status->badgeColor() }}-50 border-{{ $member->membership_status->badgeColor() }}-200 text-{{ $member->membership_status->badgeColor() }}-800">
                                        {{ $member->membership_status->label() }}
                                    </span>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('members.show', $member) }}"
                                           class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 underline">
                                            Detail
                                        </a>
                                        @can('update', $member)
                                            <a href="{{ route('members.edit', $member) }}"
                                               class="text-xs font-semibold text-gray-600 hover:text-gray-900 drawer-link">
                                                Edit
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card List (Khusus Tampilan HP / Petugas Lapangan) --}}
            <div class="block md:hidden divide-y divide-gray-200">
                @foreach($members as $member)
                    <div class="p-4 space-y-3 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-mono text-xs font-bold text-gray-500 tracking-wider">
                                    {{ $member->member_number }}
                                </span>
                                <h3 class="font-semibold text-gray-900 text-sm mt-0.5">
                                    {{ $member->full_name }}
                                </h3>
                            </div>
                            <span class="badge border shrink-0 bg-{{ $member->membership_status->badgeColor() }}-50 border-{{ $member->membership_status->badgeColor() }}-200 text-{{ $member->membership_status->badgeColor() }}-800">
                                {{ $member->membership_status->label() }}
                            </span>
                        </div>

                        @if($member->address || $member->phone)
                            <div class="text-xs text-gray-600 space-y-1.5 bg-gray-50 p-2.5 border border-gray-100">
                                @if($member->phone)
                                    <div class="flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <a href="tel:{{ $member->phone }}" class="font-mono text-emerald-800 font-semibold hover:underline">
                                            {{ $member->phone }}
                                        </a>
                                    </div>
                                @endif
                                @if($member->address)
                                    <div class="flex items-start text-gray-500">
                                        <svg class="w-3.5 h-3.5 mr-1.5 mt-0.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="line-clamp-2 leading-relaxed">{{ $member->address }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                            <a href="{{ route('members.show', $member) }}"
                               class="btn-secondary text-xs py-1.5 px-3 font-semibold text-emerald-800 border-emerald-300 hover:bg-emerald-50">
                                Detail
                            </a>
                            @can('update', $member)
                                <a href="{{ route('members.edit', $member) }}"
                                   class="btn-secondary text-xs py-1.5 px-3 drawer-link">
                                    Edit
                                </a>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>

            @if($members->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $members->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
