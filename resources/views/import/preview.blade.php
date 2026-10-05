@extends('layouts.app')

@section('title', 'Pratinjau Impor Data')
@section('page-title', 'Pratinjau & Validasi Impor')

@section('content')
<div class="py-4 space-y-6">

    {{-- Breadcrumb / Header --}}
    <div class="flex items-center justify-between border-b border-gray-200 pb-4">
        <div>
            <h2 class="text-base font-semibold text-gray-900 tracking-tight">
                Pratinjau Impor {{ $type === 'members' ? 'Anggota' : 'Usaha' }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                Periksa status validasi data di bawah ini sebelum menyimpan ke basis data
            </p>
        </div>
        <a href="{{ route('import.index') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Summary Statistics Cards --}}
    @php
        $totalRows = count($rows);
        $validCount = collect($rows)->where('is_valid', true)->count();
        $invalidCount = $totalRows - $validCount;
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 p-4">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Baris CSV</span>
            <span class="text-2xl font-bold font-mono text-gray-900 mt-1 block">{{ $totalRows }}</span>
        </div>
        <div class="bg-white border border-gray-200 p-4">
            <span class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider block">Baris Valid (Siap Disimpan)</span>
            <span class="text-2xl font-bold font-mono text-emerald-700 mt-1 block">{{ $validCount }}</span>
        </div>
        <div class="bg-white border border-gray-200 p-4">
            <span class="text-[11px] font-semibold text-rose-600 uppercase tracking-wider block">Baris Bermasalah (Akan Dilewati)</span>
            <span class="text-2xl font-bold font-mono text-rose-700 mt-1 block">{{ $invalidCount }}</span>
        </div>
    </div>

    {{-- Confirmation Bar --}}
    <div class="bg-white border border-gray-200 p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-xs text-gray-600">
            @if ($validCount > 0)
                <span class="font-semibold text-emerald-800">Siap dieksekusi:</span>
                Sebanyak <strong>{{ $validCount }}</strong> baris valid akan dimasukkan ke database secara transaksional.
                @if ($invalidCount > 0)
                    <span class="text-amber-700">({{ $invalidCount }} baris yang memiliki error otomatis diabaikan).</span>
                @endif
            @else
                <span class="font-semibold text-rose-700">Tidak ada baris data yang valid untuk disimpan.</span> Silakan perbaiki file CSV dan unggah ulang.
            @endif
        </div>

        @if ($validCount > 0)
            <form action="{{ route('import.confirm') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memproses impor {{ $validCount }} data ini?');">
                @csrf
                <input type="hidden" name="import_token" value="{{ $importToken }}">
                <button type="submit"
                        class="px-4 py-2 bg-[#006633] text-white text-xs font-semibold hover:bg-[#00552b] transition-colors shadow-sm flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan {{ $validCount }} Data ke Database
                </button>
            </form>
        @endif
    </div>

    {{-- Data Preview Table --}}
    <div class="bg-white border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Rincian Pratinjau Baris</h3>
            <span class="text-[11px] text-gray-500 font-mono">Status per baris CSV</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-[10px] tracking-wider border-b border-gray-200 font-semibold">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">Baris</th>
                        <th class="px-4 py-3 w-28">Status</th>
                        @if ($type === 'members')
                            <th class="px-4 py-3">No. Anggota</th>
                            <th class="px-4 py-3">Nama Lengkap</th>
                            <th class="px-4 py-3">Alamat</th>
                            <th class="px-4 py-3">No. Telepon</th>
                        @else
                            <th class="px-4 py-3">No. Anggota</th>
                            <th class="px-4 py-3">Nama Usaha</th>
                            <th class="px-4 py-3">Jenis Usaha</th>
                            <th class="px-4 py-3">Modal Awal</th>
                        @endif
                        <th class="px-4 py-3">Keterangan / Kesalahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-mono">
                    @forelse ($rows as $item)
                        <tr class="{{ $item['is_valid'] ? 'hover:bg-gray-50' : 'bg-rose-50/50 hover:bg-rose-50' }}">
                            <td class="px-4 py-2.5 text-center text-gray-500 font-semibold">
                                {{ $item['row_number'] }}
                            </td>
                            <td class="px-4 py-2.5 font-sans">
                                @if ($item['is_valid'])
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Valid
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold bg-rose-100 text-rose-800">
                                        Error
                                    </span>
                                @endif
                            </td>

                            @if ($type === 'members')
                                <td class="px-4 py-2.5 font-medium text-gray-900 font-mono">
                                    {{ $item['data']['member_number'] ?: '-' }}
                                </td>
                                <td class="px-4 py-2.5 text-gray-800 font-sans">
                                    {{ $item['data']['full_name'] ?: '-' }}
                                </td>
                                <td class="px-4 py-2.5 text-gray-600 font-sans truncate max-w-xs">
                                    {{ $item['data']['address'] ?: '-' }}
                                </td>
                                <td class="px-4 py-2.5 text-gray-600 font-mono">
                                    {{ $item['data']['phone'] ?: '-' }}
                                </td>
                            @else
                                <td class="px-4 py-2.5 font-mono text-gray-900">
                                    {{ $item['data']['member_no'] ?: '-' }}
                                </td>
                                <td class="px-4 py-2.5 text-gray-800 font-sans font-medium">
                                    {{ $item['data']['name'] ?: '-' }}
                                </td>
                                <td class="px-4 py-2.5 text-gray-600 font-sans">
                                    {{ $item['data']['business_type'] ?: '-' }}
                                </td>
                                <td class="px-4 py-2.5 text-gray-600 font-mono">
                                    Rp {{ number_format($item['data']['initial_capital'] ?? 0, 0, ',', '.') }}
                                </td>
                            @endif

                            <td class="px-4 py-2.5 font-sans">
                                @if ($item['is_valid'])
                                    <span class="text-gray-400 text-[11px]">-</span>
                                @else
                                    <ul class="text-rose-700 text-[11px] list-disc list-inside space-y-0.5">
                                        @foreach ($item['errors'] as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400 font-sans">
                                Tidak ada data yang ditemukan di berkas CSV.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
