@extends('layouts.app')

@section('title', 'Edit Rekomendasi: ' . $coaching->title)
@section('page-title', 'Edit Rekomendasi Pembinaan')

@section('content')
<div class="py-4 space-y-6 max-w-3xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center space-x-2 text-xs text-gray-500 border-b border-gray-200 pb-3">
        <a href="{{ route('coaching.index') }}" class="hover:text-emerald-700 transition">Pembinaan</a>
        <span>/</span>
        <a href="{{ route('coaching.show', $coaching) }}" class="hover:text-emerald-700 transition truncate max-w-xs">{{ $coaching->title }}</a>
        <span>/</span>
        <span class="text-gray-900 font-semibold">Edit</span>
    </div>

    {{-- Form --}}
    <div class="bg-white border border-gray-200 p-6 md:p-8">
        <div class="border-b border-gray-200 pb-4 mb-6">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wider">Edit Rekomendasi Pembinaan</h3>
            <p class="text-xs text-gray-500 mt-0.5">Ubah detail rekomendasi dan status pembinaan.</p>
        </div>

        <form action="{{ route('coaching.update', $coaching) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                        Kategori Pembinaan <span class="text-red-500">*</span>
                    </label>
                    <select id="category" name="category" required class="input-base">
                        @foreach(['Keuangan & Pembukuan', 'Pemasaran & Penjualan', 'Operasional Usaha', 'SDM & Manajemen', 'Kepatuhan Angsuran', 'Legalitas & Perizinan', 'Produksi & Kualitas', 'Lainnya'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $coaching->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required class="input-base">
                        <option value="pending" {{ old('status', $coaching->status->value) === 'pending' ? 'selected' : '' }}>Belum Dimulai</option>
                        <option value="in_progress" {{ old('status', $coaching->status->value) === 'in_progress' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="done" {{ old('status', $coaching->status->value) === 'done' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    @error('status') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                    Judul Rekomendasi <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $coaching->title) }}" required
                       class="input-base">
                @error('title') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                    Deskripsi Pembinaan <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4" required
                          class="input-base resize-none">{{ old('description', $coaching->description) }}</textarea>
                @error('description') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="reason" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                    Alasan / Latar Belakang
                </label>
                <textarea id="reason" name="reason" rows="3"
                          class="input-base resize-none">{{ old('reason', $coaching->reason) }}</textarea>
                @error('reason') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('coaching.show', $coaching) }}" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
