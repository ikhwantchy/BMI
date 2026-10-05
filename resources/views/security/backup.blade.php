@extends('layouts.app')

@section('title', 'Cadangan Data')
@section('page-title', 'Cadangan Data (Backup)')

@section('content')
<div class="py-4 space-y-6">

    {{-- Header --}}
    <div class="border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900 tracking-tight">Cadangan Database</h2>
            <p class="text-xs text-gray-500 mt-0.5">Buat dan kelola snapshot database untuk pemulihan data saat darurat</p>
        </div>
        <form method="POST" action="{{ route('security.backup.create') }}" id="form-create-backup"
              onsubmit="document.getElementById('btn-backup').disabled=true; document.getElementById('btn-backup').textContent='Memproses...';">
            @csrf
            <button type="submit" id="btn-backup"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#006633] text-white text-xs font-semibold hover:bg-[#00552b] transition-colors shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                Buat Backup Sekarang
            </button>
        </form>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 text-xs px-4 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Status Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Database Status --}}
        <div class="bg-white border border-gray-200 p-4">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 {{ $dbOk ? 'bg-emerald-50' : 'bg-red-50' }} flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 {{ $dbOk ? 'text-emerald-500' : 'text-red-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Database</p>
                    <p class="text-sm font-bold {{ $dbOk ? 'text-emerald-600' : 'text-red-600' }} mt-0.5">
                        {{ $dbOk ? 'Terhubung' : 'Tidak Terhubung' }}
                    </p>
                    <p class="text-[11px] text-gray-400 mt-0.5 font-mono">{{ strtoupper($dbConfig['driver']) }}: {{ $dbConfig['database'] }}</p>
                </div>
            </div>
        </div>

        {{-- Storage Status --}}
        <div class="bg-white border border-gray-200 p-4">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 {{ $storageOk ? 'bg-emerald-50' : 'bg-red-50' }} flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 {{ $storageOk ? 'text-emerald-500' : 'text-red-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Storage</p>
                    <p class="text-sm font-bold {{ $storageOk ? 'text-emerald-600' : 'text-red-600' }} mt-0.5">
                        {{ $storageOk ? 'Dapat Ditulis' : 'Read-Only' }}
                    </p>
                    <p class="text-[11px] text-gray-400 mt-0.5 font-mono">storage/app/backups</p>
                </div>
            </div>
        </div>

        {{-- Total Backups --}}
        <div class="bg-white border border-gray-200 p-4">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">File Backup</p>
                    <p class="text-2xl font-bold text-blue-600 mt-0.5">{{ count($backups) }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Snapshot tersimpan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Backup Info Box --}}
    <div class="bg-amber-50 border border-amber-200 px-4 py-3 flex items-start gap-3">
        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-[11px] text-amber-800 space-y-1">
            <p class="font-semibold">Catatan Penting:</p>
            <ul class="list-disc list-inside space-y-0.5">
                <li>Backup memerlukan <code class="font-mono bg-amber-100 px-1">mysqldump</code> tersedia di server.</li>
                <li>File disimpan di <code class="font-mono bg-amber-100 px-1">storage/app/backups/</code> dan tidak dapat diakses publik.</li>
                <li>Disarankan untuk memindahkan backup ke lokasi eksternal (cloud/NAS) secara berkala.</li>
                <li>Untuk restore, jalankan: <code class="font-mono bg-amber-100 px-1">mysql -u user -p database &lt; backup.sql</code></li>
            </ul>
        </div>
    </div>

    {{-- Backup Files Table --}}
    <div class="bg-white border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h3 class="text-xs font-semibold text-gray-700">Daftar File Backup</h3>
            @if(count($backups) > 0)
                <span class="text-[11px] text-gray-400">Total: {{ count($backups) }} file</span>
            @endif
        </div>

        @if(count($backups) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Nama File</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Ukuran</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Dibuat</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($backups as $backup)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-[11px] text-gray-800">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        {{ $backup['filename'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    @php
                                        $sizeKb = $backup['size'] / 1024;
                                        $display = $sizeKb > 1024
                                            ? number_format($sizeKb / 1024, 2) . ' MB'
                                            : number_format($sizeKb, 1) . ' KB';
                                    @endphp
                                    {{ $display }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    <div>{{ $backup['created_at']->format('d/m/Y') }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $backup['created_at']->format('H:i:s') }} WIB</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('security.backup.download', $backup['filename']) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 text-white text-[11px] font-medium hover:bg-blue-700 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Unduh
                                        </a>
                                        <form method="POST"
                                              action="{{ route('security.backup.delete', $backup['filename']) }}"
                                              onsubmit="return confirm('Yakin hapus file backup ini? Tindakan tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 border border-red-300 text-red-600 text-[11px] font-medium hover:bg-red-50 transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-4 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                <p class="text-sm font-medium text-gray-500">Belum ada file backup tersimpan</p>
                <p class="text-xs text-gray-400 mt-1">Klik "Buat Backup Sekarang" untuk membuat snapshot pertama</p>
            </div>
        @endif
    </div>

</div>
@endsection
