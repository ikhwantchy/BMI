@extends('layouts.app')

@section('title', 'Cadangan Data & Sinkronisasi Drive')
@section('page-title', 'Cadangan Data (Backup)')

@section('content')
<div class="py-4 space-y-6">

    {{-- Header --}}
    <div class="border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900 tracking-tight">Cadangan Database & Google Drive</h2>
            <p class="text-xs text-gray-500 mt-0.5">Kelola snapshot database lokal dan pencadangan otomatis cloud ke Google Drive</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Backup ke Google Drive --}}
            <form method="POST" action="{{ route('security.backup.gdrive') }}" id="form-gdrive"
                  onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').textContent='Memproses...';">
                @csrf
                <button type="submit" id="btn-gdrive"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1a73e8] text-white text-xs font-semibold hover:bg-[#1557b0] transition-colors shadow-xs">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6.28 3L1 12.5l3.14 5.5L9.42 9 6.28 3zM17.72 3L14.58 9l5.28 9H23L17.72 3zM12 9L8.86 15h6.28L12 9z"/>
                    </svg>
                    Backup ke Google Drive
                </button>
            </form>

            {{-- Backup Lokal --}}
            <form method="POST" action="{{ route('security.backup.create') }}" id="form-create-backup"
                  onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').textContent='Memproses...';">
                @csrf
                <button type="submit" id="btn-backup"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#006633] text-white text-xs font-semibold hover:bg-[#00552b] transition-colors shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                    Backup Lokal
                </button>
            </form>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('warning'))
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs px-4 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>{{ session('warning') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 text-xs px-4 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- Status Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Storage Lokal</p>
                    <p class="text-sm font-bold {{ $storageOk ? 'text-emerald-600' : 'text-red-600' }} mt-0.5">
                        {{ $storageOk ? 'Dapat Ditulis' : 'Read-Only' }}
                    </p>
                    <p class="text-[11px] text-gray-400 mt-0.5 font-mono">storage/app/backups</p>
                </div>
            </div>
        </div>

        {{-- Google Drive Status --}}
        <div class="bg-white border border-gray-200 p-4">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 {{ $gdriveConfigured ? 'bg-blue-50' : 'bg-amber-50' }} flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 {{ $gdriveConfigured ? 'text-[#1a73e8]' : 'text-amber-500' }}" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6.28 3L1 12.5l3.14 5.5L9.42 9 6.28 3zM17.72 3L14.58 9l5.28 9H23L17.72 3zM12 9L8.86 15h6.28L12 9z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Google Drive</p>
                    <p class="text-sm font-bold {{ $gdriveConfigured ? 'text-[#1a73e8]' : 'text-amber-600' }} mt-0.5">
                        {{ $gdriveConfigured ? 'Terkoneksi' : 'Belum Diatur' }}
                    </p>
                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $gdriveConfigured ? 'Sinkron Cloud Aktif' : 'Perlu Setup Kredensial' }}</p>
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
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">File Tersimpan</p>
                    <p class="text-2xl font-bold text-blue-600 mt-0.5">{{ count($backups) }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Snapshot lokal siap diunduh</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Jadwal Backup Otomatis & Status Cloud --}}
    <div class="bg-white border border-gray-200 p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-gray-800">Pencadangan Otomatis (Cron Schedule)</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">
                        Jadwal otomatis berjalan setiap hari pukul <strong class="text-gray-700">01:00 WIB</strong> via command <code class="font-mono bg-gray-100 px-1 py-0.5 text-gray-800">backup:database</code> &amp; <code class="font-mono bg-gray-100 px-1 py-0.5 text-gray-800">backup:run-gdrive</code>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($gdriveConfigured)
                    <form method="POST" action="{{ route('security.backup.gdrive-test') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 border border-blue-300 text-[#1a73e8] text-[11px] font-semibold hover:bg-blue-50 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Uji Koneksi Drive
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Configuration Details / Setup Box --}}
        <div class="mt-3 pt-1" x-data="{ showConfigForm: false }">
            @if($gdriveConfigured)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between text-[11px] text-gray-600 gap-2">
                    <div class="space-y-0.5">
                        <p><span class="font-semibold text-gray-700">Email Service Account:</span> <code class="font-mono bg-gray-100 px-1">{{ $gdriveInfo['client_email'] ?? '-' }}</code></p>
                        <p><span class="font-semibold text-gray-700">Folder ID Google Drive:</span> <code class="font-mono bg-gray-100 px-1">{{ $gdriveInfo['folder_id'] ?? '-' }}</code></p>
                    </div>
                    <button type="button" @click="showConfigForm = !showConfigForm"
                            class="text-[#1a73e8] font-semibold hover:underline cursor-pointer self-start sm:self-auto">
                        <span x-text="showConfigForm ? 'Tutup Pengaturan' : 'Ubah Pengaturan Kredensial Drive'"></span>
                    </button>
                </div>
            @else
                <div class="text-[11px] text-amber-800 bg-amber-50 border border-amber-200 p-3 mb-3 flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <span class="font-semibold">Google Drive belum dikonfigurasi:</span>
                        Agar backup dapat diunggah otomatis ke Google Drive, masukkan Service Account JSON Key dan Folder ID di form bawah ini.
                    </div>
                </div>
            @endif

            {{-- Form Konfigurasi Google Drive --}}
            <div x-show="showConfigForm || {{ $gdriveConfigured ? 'false' : 'true' }}" class="mt-4 p-4 bg-gray-50 border border-gray-200 space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-gray-800">Form Pengaturan Google Drive Service Account</h4>
                </div>

                <form method="POST" action="{{ route('security.backup.gdrive-config') }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">
                            Google Drive Folder ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="folder_id" value="{{ old('folder_id', $gdriveInfo['folder_id'] ?? '') }}" required
                               placeholder="Contoh: 1BxiMVs0XRA5nFMdKvBHK78G8p42s..."
                               class="w-full text-xs font-mono px-3 py-2 border border-gray-300 bg-white focus:outline-none focus:border-[#1a73e8]">
                        <p class="text-[10px] text-gray-500 mt-1">Dapat diambil dari URL folder Google Drive: <code class="font-mono bg-gray-200 px-1">drive.google.com/drive/folders/<strong>[FOLDER_ID]</strong></code></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">
                                Upload File JSON Service Account
                            </label>
                            <input type="file" name="service_account_file" accept=".json,text/plain"
                                   class="w-full text-xs px-2 py-1.5 border border-gray-300 bg-white focus:outline-none">
                            <p class="text-[10px] text-gray-500 mt-1">File key JSON dari Google Cloud Console (IAM &amp; Admin &gt; Service Accounts &gt; Keys).</p>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">
                                Atau Tempel (Paste) Isi JSON Key
                            </label>
                            <textarea name="service_account_json" rows="2" placeholder='{"type": "service_account", "project_id": ...}'
                                      class="w-full text-[11px] font-mono px-3 py-1.5 border border-gray-300 bg-white focus:outline-none"></textarea>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-between">
                        <div class="text-[11px] text-gray-500">
                            <strong>Catatan:</strong> Folder Google Drive wajib di-share ke email Service Account dengan izin <em>Editor</em>.
                        </div>
                        <button type="submit" class="px-4 py-2 bg-[#1a73e8] text-white text-xs font-semibold hover:bg-[#1557b0] transition-colors">
                            Simpan Pengaturan Google Drive
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Backup Files Table (LOKAL) --}}
    <div class="bg-white border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-[#006633]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                <h3 class="text-xs font-semibold text-gray-800">Daftar File Cadangan (Penyimpanan Lokal Server)</h3>
            </div>
            @if(count($backups) > 0)
                <span class="text-[11px] text-gray-500 font-medium">Total: {{ count($backups) }} file snapshot</span>
            @endif
        </div>

        @if(count($backups) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Nama File Backup</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Format</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Ukuran</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Dibuat Pada</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Aksi &amp; Unduh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($backups as $backup)
                            @php
                                $ext = strtolower(pathinfo($backup['filename'], PATHINFO_EXTENSION));
                                $sizeKb = $backup['size'] / 1024;
                                $displaySize = $sizeKb > 1024
                                    ? number_format($sizeKb / 1024, 2) . ' MB'
                                    : number_format($sizeKb, 1) . ' KB';
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-[11px] text-gray-900 font-medium">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        {{ $backup['filename'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 text-[10px] font-bold font-mono uppercase bg-gray-100 text-gray-700 border border-gray-200">
                                        .{{ $ext }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700 font-mono text-[11px]">
                                    {{ $displaySize }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-[11px]">
                                    <div>{{ $backup['created_at']->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $backup['created_at']->format('H:i:s') }} WIB</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        {{-- 1. Tombol UNDUH / DOWNLOAD LANGSUNG --}}
                                        <a href="{{ route('security.backup.download', $backup['filename']) }}"
                                           download="{{ $backup['filename'] }}"
                                           title="Unduh file backup ke komputer"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#006633] text-white text-[11px] font-semibold hover:bg-[#00552b] transition-colors shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Unduh
                                        </a>

                                        {{-- 2. Tombol UPLOAD KE GOOGLE DRIVE --}}
                                        @if($gdriveConfigured)
                                            <form method="POST" action="{{ route('security.backup.gdrive-upload', $backup['filename']) }}"
                                                  onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').textContent='Mengunggah...';">
                                                @csrf
                                                <button type="submit"
                                                        title="Kirim snapshot ini ke Google Drive"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-[#1a73e8] text-white text-[11px] font-semibold hover:bg-[#1557b0] transition-colors shadow-xs">
                                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M6.28 3L1 12.5l3.14 5.5L9.42 9 6.28 3zM17.72 3L14.58 9l5.28 9H23L17.72 3zM12 9L8.86 15h6.28L12 9z"/>
                                                    </svg>
                                                    Ke Drive
                                                </button>
                                            </form>
                                        @endif

                                        {{-- 3. Tombol HAPUS --}}
                                        <form method="POST"
                                              action="{{ route('security.backup.delete', $backup['filename']) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus file backup {{ $backup['filename'] }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Hapus file backup dari penyimpanan server"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 border border-red-300 text-red-600 text-[11px] font-medium hover:bg-red-50 transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                <p class="text-sm font-medium text-gray-500">Belum ada file backup di penyimpanan lokal server</p>
                <p class="text-xs text-gray-400 mt-1">Klik tombol <strong>"Backup Lokal"</strong> di kanan atas untuk membuat snapshot pertama</p>
            </div>
        @endif
    </div>

    {{-- File di Google Drive (jika ada) --}}
    @if($gdriveConfigured && !empty($gdriveFiles))
    <div class="bg-white border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-blue-50/50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-[#1a73e8]" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6.28 3L1 12.5l3.14 5.5L9.42 9 6.28 3zM17.72 3L14.58 9l5.28 9H23L17.72 3zM12 9L8.86 15h6.28L12 9z"/>
                </svg>
                <h3 class="text-xs font-semibold text-gray-800">File Backup di Google Drive Cloud</h3>
            </div>
            <span class="text-[11px] text-gray-500 font-medium">Tersimpan di Cloud</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Nama File</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Ukuran</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Waktu Upload</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($gdriveFiles as $dFile)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-[11px] text-gray-800">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-[#1a73e8] shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M6.28 3L1 12.5l3.14 5.5L9.42 9 6.28 3z"/>
                                    </svg>
                                    {{ $dFile['name'] }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 font-mono">
                                {{ number_format($dFile['size'] / 1024, 1) }} KB
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $dFile['created_at'] ? $dFile['created_at']->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') . ' WIB' : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($dFile['web_link'])
                                    <a href="{{ $dFile['web_link'] }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-[#1a73e8] border border-blue-200 text-[11px] font-semibold hover:bg-blue-100 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        Buka di Drive
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Petunjuk Pemulihan / Restore --}}
    <div class="bg-gray-50 border border-gray-200 px-4 py-3.5">
        <div class="flex items-start gap-3">
            <div class="w-6 h-6 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-[11px] text-gray-600 space-y-1">
                <p class="font-semibold text-gray-800">Petunjuk Pemulihan Data (Restore):</p>
                <ol class="list-decimal list-inside space-y-0.5 text-gray-600">
                    <li>Unduh file snapshot backup terbaru menggunakan tombol <strong>"Unduh"</strong> di atas.</li>
                    <li>Untuk database MySQL di server, jalankan perintah terminal:
                        <code class="font-mono bg-gray-200 text-gray-800 px-1 py-0.5 ml-1">mysql -u [user] -p [database] &lt; backup_file.sql</code>
                    </li>
                    <li>Jika file terkompresi (<code class="font-mono">.gz</code>), ekstrak terlebih dahulu: <code class="font-mono bg-gray-200 text-gray-800 px-1 py-0.5">gunzip backup_file.sql.gz</code></li>
                </ol>
            </div>
        </div>
    </div>

</div>
@endsection
