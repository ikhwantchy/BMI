@extends('layouts.app')

@section('title', 'Jejak Audit & Keamanan')
@section('page-title', 'Jejak Audit (Audit Trail)')

@section('content')
<div class="py-4 space-y-6">

    {{-- Header --}}
    <div class="border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900 tracking-tight">Log Aktivitas & Jejak Keamanan</h2>
            <p class="text-xs text-gray-500 mt-0.5">Catatan seluruh transaksi, pengajuan, validasi, penolakan, serta autentikasi pengguna</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('audit.export', request()->query()) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#006633] text-white text-xs font-medium hover:bg-[#00552b] transition-colors shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Ekspor CSV
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white border border-gray-200 p-4">
        <form method="GET" action="{{ route('audit.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            @if(auth()->user()->hasAnyRole(['system_admin', 'pengurus', 'pengawas']))
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Cabang</label>
                    <select name="branch_id" class="w-full text-xs border border-gray-300 px-2.5 py-1.5 focus:border-[#006633] focus:ring-0">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->code }} — {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Jenis Aksi</label>
                <select name="action" class="w-full text-xs border border-gray-300 px-2.5 py-1.5 focus:border-[#006633] focus:ring-0">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $actKey => $actLabel)
                        <option value="{{ $actKey }}" {{ request('action') === $actKey ? 'selected' : '' }}>
                            {{ $actLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full text-xs border border-gray-300 px-2.5 py-1.5 focus:border-[#006633] focus:ring-0">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full text-xs border border-gray-300 px-2.5 py-1.5 focus:border-[#006633] focus:ring-0">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Pencarian</label>
                <div class="flex gap-1.5">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="User, IP, info..."
                           class="w-full text-xs border border-gray-300 px-2.5 py-1.5 focus:border-[#006633] focus:ring-0">
                    <button type="submit"
                            class="px-3 py-1.5 bg-gray-800 text-white text-xs font-medium hover:bg-black transition-colors shrink-0">
                        Cari
                    </button>
                    @if(request()->anyFilled(['branch_id', 'action', 'date_from', 'date_to', 'search']))
                        <a href="{{ route('audit.index') }}"
                           class="px-2 py-1.5 border border-gray-300 text-gray-600 text-xs hover:bg-gray-50 shrink-0" title="Reset filter">
                            ✕
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Audit Log Table --}}
    <div class="bg-white border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Waktu</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Aksi</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Pengguna</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Cabang</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Entitas</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">IP & Info</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                        @php
                            $badgeColor = match($log->action) {
                                'login'        => 'bg-blue-50 text-blue-800 border-blue-200',
                                'failed_login' => 'bg-red-50 text-red-800 border-red-200 font-bold',
                                'logout'       => 'bg-gray-100 text-gray-700 border-gray-300',
                                'create'       => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'update'       => 'bg-amber-50 text-amber-800 border-amber-200',
                                'delete'       => 'bg-red-50 text-red-800 border-red-200',
                                'submit'       => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                                'validate'     => 'bg-emerald-100 text-emerald-900 border-emerald-300 font-semibold',
                                'reject'       => 'bg-rose-100 text-rose-900 border-rose-300 font-semibold',
                                'revise'       => 'bg-orange-100 text-orange-900 border-orange-300 font-semibold',
                                default        => 'bg-gray-50 text-gray-700 border-gray-200',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap font-mono text-[11px] text-gray-600">
                                <div>{{ $log->created_at->format('d/m/Y') }}</div>
                                <div class="text-gray-400">{{ $log->created_at->format('H:i:s') }} WIB</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-block px-2 py-0.5 text-[10px] uppercase font-mono tracking-wider border {{ $badgeColor }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($log->user)
                                    <div class="font-medium text-gray-900">{{ $log->user->name }}</div>
                                    <div class="text-[11px] text-gray-500 font-mono">{{ $log->user->username }} ({{ $log->user->roleLabel() }})</div>
                                @else
                                    <span class="text-gray-400 italic">Sistem / Tamu</span>
                                    @if(isset($log->old_values['attempted_identifier']))
                                        <div class="text-[10px] text-red-600 font-mono">Percobaan: {{ $log->old_values['attempted_identifier'] }}</div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                {{ $log->branch?->name ?? ($log->user?->branch?->name ?? 'Pusat') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                @if($log->entity_type)
                                    <span class="font-mono text-[11px]">{{ class_basename($log->entity_type) }}</span>
                                    @if($log->entity_id)
                                        <span class="text-gray-400 font-mono">#{{ $log->entity_id }}</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($log->context)
                                    <div class="font-medium text-gray-900">{{ $log->context }}</div>
                                @endif
                                <div class="text-[10px] text-gray-400 font-mono mt-0.5">
                                    IP: {{ $log->ip_address ?? '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">
                                Belum ada catatan jejak audit yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="border-t border-gray-200 p-3 bg-gray-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
