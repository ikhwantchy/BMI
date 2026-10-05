@extends('layouts.app')

@section('title', 'Tambah Rekomendasi Pembinaan')
@section('page-title', 'Tambah Rekomendasi Pembinaan')

@section('content')
<div class="py-4 space-y-6 max-w-3xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center space-x-2 text-xs text-gray-500 border-b border-gray-200 pb-3">
        <a href="{{ route('coaching.index') }}" class="hover:text-emerald-700 transition">Pembinaan</a>
        <span>/</span>
        <span class="text-gray-900 font-semibold">Tambah Rekomendasi</span>
    </div>

    {{-- Info Evaluasi --}}
    <div class="bg-gray-50 border border-gray-200 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <p class="text-[10px] font-semibold text-emerald-800 uppercase tracking-wider">Evaluasi Terkait</p>
                <h3 class="font-semibold text-gray-900 mt-0.5 text-sm">{{ $evaluation->business->name }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Anggota: {{ $evaluation->business->member->full_name }}
                    &bull; Skor: <span class="font-semibold font-mono text-emerald-800">{{ number_format($evaluation->final_score, 1) }}</span>
                    @if($evaluation->recommendation)
                        &bull; Rekomendasi:
                        <span class="font-semibold text-gray-800">{{ $evaluation->recommendation->label() }}</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('evaluations.show', $evaluation) }}" class="btn-secondary text-xs">
                Lihat Evaluasi &rarr;
            </a>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white border border-gray-200 p-6 md:p-8">
        <div class="border-b border-gray-200 pb-4 mb-6">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wider">Form Rekomendasi Pembinaan</h3>
            <p class="text-xs text-gray-500 mt-0.5">Isi detail rekomendasi tindak lanjut pembinaan untuk usaha ini.</p>
        </div>

        <form action="{{ route('coaching.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="evaluation_id" value="{{ $evaluation->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                        Kategori Pembinaan <span class="text-red-500">*</span>
                    </label>
                    <select id="category" name="category" required class="input-base">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach(['Keuangan & Pembukuan', 'Pemasaran & Penjualan', 'Operasional Usaha', 'SDM & Manajemen', 'Kepatuhan Angsuran', 'Legalitas & Perizinan', 'Produksi & Kualitas', 'Lainnya'] as $cat)
                            <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                        Judul Rekomendasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                           placeholder="Contoh: Pelatihan Pembukuan Sederhana"
                           class="input-base">
                    @error('title') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                    Deskripsi Pembinaan <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4" required
                          placeholder="Jelaskan secara rinci kegiatan pembinaan yang direkomendasikan..."
                          class="input-base resize-none">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="reason" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                    Alasan / Latar Belakang
                </label>
                <textarea id="reason" name="reason" rows="3"
                          placeholder="Tuliskan temuan atau kondisi usaha yang menjadi dasar rekomendasi ini..."
                          class="input-base resize-none">{{ old('reason') }}</textarea>
                @error('reason') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('evaluations.show', $evaluation) }}" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    Simpan Rekomendasi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
