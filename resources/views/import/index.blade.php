@extends('layouts.app')

@section('title', 'Impor Data')
@section('page-title', 'Impor Data Massal (CSV)')

@section('content')
<div class="py-4 space-y-6 max-w-4xl">

    {{-- Header --}}
    <div class="border-b border-gray-200 pb-4">
        <h2 class="text-base font-semibold text-gray-900 tracking-tight">Impor Data Anggota & Usaha</h2>
        <p class="text-xs text-gray-500 mt-0.5">Unggah berkas CSV untuk memasukkan data secara massal dengan verifikasi bertahap</p>
    </div>

    {{-- Step Instructions --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 p-4">
            <span class="w-6 h-6 flex items-center justify-center bg-gray-100 text-gray-800 font-mono font-bold text-xs mb-2">1</span>
            <h3 class="text-xs font-semibold text-gray-900">Unduh Format Template</h3>
            <p class="text-[11px] text-gray-500 mt-1">Gunakan template CSV resmi agar kolom sesuai dengan struktur sistem.</p>
        </div>
        <div class="bg-white border border-gray-200 p-4">
            <span class="w-6 h-6 flex items-center justify-center bg-gray-100 text-gray-800 font-mono font-bold text-xs mb-2">2</span>
            <h3 class="text-xs font-semibold text-gray-900">Validasi & Pratinjau</h3>
            <p class="text-[11px] text-gray-500 mt-1">Sistem akan memeriksa integritas data dan mendeteksi duplikasi sebelum disimpan.</p>
        </div>
        <div class="bg-white border border-gray-200 p-4">
            <span class="w-6 h-6 flex items-center justify-center bg-gray-100 text-gray-800 font-mono font-bold text-xs mb-2">3</span>
            <h3 class="text-xs font-semibold text-gray-900">Konfirmasi Transaksional</h3>
            <p class="text-[11px] text-gray-500 mt-1">Data valid dimasukkan ke database secara transaksional dan tercatat di jejak audit.</p>
        </div>
    </div>

    {{-- Form Upload & Download Templates --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Impor Anggota --}}
        <div class="bg-white border border-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Impor Data Anggota</h3>
                    <p class="text-[11px] text-gray-500">Berkas CSV berisi data anggota baru</p>
                </div>
                <a href="{{ route('import.template', 'members') }}"
                   class="inline-flex items-center gap-1 text-[11px] text-[#006633] font-semibold hover:underline">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh Template CSV
                </a>
            </div>

            <form action="{{ route('import.preview') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="type" value="members">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih Berkas CSV Anggota (.csv)</label>
                    <input type="file" name="file" accept=".csv" required
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                </div>

                <button type="submit"
                        class="w-full py-2 bg-[#006633] text-white text-xs font-semibold hover:bg-[#00552b] transition-colors cursor-pointer">
                    Unggah & Pratinjau Anggota
                </button>
            </form>
        </div>

        {{-- Impor Usaha --}}
        <div class="bg-white border border-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Impor Data Usaha</h3>
                    <p class="text-[11px] text-gray-500">Berkas CSV berisi data usaha anggota</p>
                </div>
                <a href="{{ route('import.template', 'businesses') }}"
                   class="inline-flex items-center gap-1 text-[11px] text-[#006633] font-semibold hover:underline">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh Template CSV
                </a>
            </div>

            <form action="{{ route('import.preview') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="type" value="businesses">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih Berkas CSV Usaha (.csv)</label>
                    <input type="file" name="file" accept=".csv" required
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                </div>

                <button type="submit"
                        class="w-full py-2 bg-[#006633] text-white text-xs font-semibold hover:bg-[#00552b] transition-colors cursor-pointer">
                    Unggah & Pratinjau Usaha
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
