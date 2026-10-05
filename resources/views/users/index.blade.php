@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Kelola Pengguna Sistem')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-1">
        <div>
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Daftar Pengguna</h2>
            <p class="text-xs text-gray-500 mt-1">Kelola akun petugas, asisten, manajer, pimpinan, dan auditor sistem</p>
        </div>
        @if(auth()->user()->canManageUsers())
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#009a4c] hover:bg-[#007a3d] text-white text-xs font-semibold rounded-xl shadow-xs hover:shadow transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Pengguna</span>
            </a>
        @endif
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 p-4 sm:p-5 shadow-xs">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            @if(auth()->user()->hasAnyRole(['system_admin', 'pengurus', 'pengawas']))
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Cabang</label>
                    <select name="branch_id" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2 text-gray-700 focus:bg-white focus:border-[#009a4c] focus:ring-1 focus:ring-[#009a4c] transition-all">
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
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Role / Peran</label>
                <select name="role" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2 text-gray-700 focus:bg-white focus:border-[#009a4c] focus:ring-1 focus:ring-[#009a4c] transition-all">
                    <option value="">Semua Peran</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status Akun</label>
                <select name="status" class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2 text-gray-700 focus:bg-white focus:border-[#009a4c] focus:ring-1 focus:ring-[#009a4c] transition-all">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Pencarian</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nama, username, email..."
                           class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2 text-gray-700 focus:bg-white focus:border-[#009a4c] focus:ring-1 focus:ring-[#009a4c] transition-all">
                    <button type="submit"
                            class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-xl transition-colors shrink-0 shadow-xs">
                        Cari
                    </button>
                    @if(request()->anyFilled(['branch_id', 'role', 'status', 'search']))
                        <a href="{{ route('users.index') }}"
                           class="px-3 py-2 border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-100 text-xs rounded-xl transition-colors shrink-0" title="Reset filter">
                            ✕
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-xs">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-5 py-3.5 text-left font-bold text-gray-500 uppercase tracking-wider text-[11px]">Nama & Username</th>
                        <th class="px-5 py-3.5 text-left font-bold text-gray-500 uppercase tracking-wider text-[11px]">Email</th>
                        <th class="px-5 py-3.5 text-left font-bold text-gray-500 uppercase tracking-wider text-[11px]">Peran (Role)</th>
                        <th class="px-5 py-3.5 text-left font-bold text-gray-500 uppercase tracking-wider text-[11px]">Cabang</th>
                        <th class="px-5 py-3.5 text-left font-bold text-gray-500 uppercase tracking-wider text-[11px]">Status</th>
                        <th class="px-5 py-3.5 text-right font-bold text-gray-500 uppercase tracking-wider text-[11px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                <div class="text-[11px] text-gray-400 font-mono mt-0.5">{{ '@' . $user->username }}</div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-gray-600 font-mono text-[11px]">
                                {{ $user->email }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase font-mono tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    {{ $user->roleLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-gray-700 font-medium">
                                {{ $user->branch?->name ?? 'Pusat / Lintas Cabang' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right space-x-1.5">
                                @if(auth()->user()->canManageUsers())
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="inline-flex items-center px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-xs transition-colors">
                                        Edit
                                    </a>

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.toggle', $user) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Ubah status aktif akun ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center px-2.5 py-1 {{ $user->status === 'active' ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200' }} font-semibold rounded-lg text-xs transition-colors cursor-pointer">
                                                {{ $user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-400 italic">
                                Tidak ada data pengguna yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-gray-100 px-5 py-3.5 bg-gray-50/80">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

</div>
@endsection
