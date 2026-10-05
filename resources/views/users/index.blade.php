@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Kelola Pengguna Sistem')

@section('content')
<div class="py-4 space-y-6">

    {{-- Header --}}
    <div class="border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900 tracking-tight">Daftar Pengguna</h2>
            <p class="text-xs text-gray-500 mt-0.5">Kelola akun petugas, asisten, manajer, pimpinan, dan auditor</p>
        </div>
        @if(auth()->user()->canManageUsers())
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#009a4c] text-white text-xs font-semibold hover:bg-[#007d3e] transition-colors shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Pengguna
            </a>
        @endif
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white border border-gray-200 p-4">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @if(auth()->user()->hasAnyRole(['system_admin', 'pengurus', 'pengawas']))
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Cabang</label>
                    <select name="branch_id" class="w-full text-xs border border-gray-300 px-2.5 py-1.5 focus:border-[#006633] focus:ring-0">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->code }} — {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Role / Peran</label>
                <select name="role" class="w-full text-xs border border-gray-300 px-2.5 py-1.5 focus:border-[#006633] focus:ring-0">
                    <option value="">Semua Peran</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full text-xs border border-gray-300 px-2.5 py-1.5 focus:border-[#006633] focus:ring-0">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Pencarian</label>
                <div class="flex gap-1.5">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nama, username, email..."
                           class="w-full text-xs border border-gray-300 px-2.5 py-1.5 focus:border-[#006633] focus:ring-0">
                    <button type="submit"
                            class="px-3 py-1.5 bg-gray-800 text-white text-xs font-medium hover:bg-black transition-colors shrink-0">
                        Cari
                    </button>
                    @if(request()->anyFilled(['branch_id', 'role', 'status', 'search']))
                        <a href="{{ route('users.index') }}"
                           class="px-2 py-1.5 border border-gray-300 text-gray-600 text-xs hover:bg-gray-50 shrink-0" title="Reset filter">
                            ✕
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Nama & Username</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Email</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Peran (Role)</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Cabang</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-[11px] text-gray-400 font-mono">{{ $user->username }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600 font-mono text-[11px]">
                                {{ $user->email }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-block px-2 py-0.5 text-[10px] uppercase font-mono tracking-wider border bg-emerald-50 text-emerald-800 border-emerald-200">
                                    {{ $user->roleLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                {{ $user->branch?->name ?? 'Pusat / Lintas Cabang' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-700 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] text-gray-400 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right space-x-2">
                                @if(auth()->user()->canManageUsers())
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.toggle', $user) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Ubah status aktif akun ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs {{ $user->status === 'active' ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }} font-medium cursor-pointer">
                                                {{ $user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">
                                Tidak ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-gray-200 p-3 bg-gray-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
