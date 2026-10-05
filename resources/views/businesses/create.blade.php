@extends('layouts.app')

@section('title', 'Tambah Usaha Baru')
@section('page-title', 'Tambah Usaha')

@section('content')
<div class="py-4 max-w-2xl">

    <div class="bg-white border border-gray-200 p-6">
        <div class="border-b border-gray-200 pb-4 mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Form Pendaftaran Usaha</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftarkan data unit usaha anggota untuk dievaluasi</p>
            </div>
            <a href="{{ route('businesses.index') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('businesses.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Pemilik Usaha (Anggota) --}}
            <div>
                <label for="member_id" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Anggota Pemilik Usaha <span class="text-red-500">*</span>
                </label>
                <select name="member_id"
                        id="member_id"
                        required
                        class="input-base @error('member_id') border-red-500 bg-red-50 @enderror">
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}"
                            {{ old('member_id', $selectedMember?->id) == $m->id ? 'selected' : '' }}>
                            {{ $m->member_number }} &mdash; {{ $m->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('member_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama Usaha --}}
            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Nama Usaha / Toko <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name') }}"
                       placeholder="Contoh: Toko Sembako Berkah"
                       required
                       class="input-base @error('name') border-red-500 bg-red-50 @enderror">
                @error('name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jenis Usaha & Usia Usaha --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="business_type" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                        Jenis / Bidang Usaha <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="business_type"
                           id="business_type"
                           value="{{ old('business_type') }}"
                           placeholder="Contoh: Perdagangan, Jasa, Tekstil"
                           required
                           class="input-base @error('business_type') border-red-500 bg-red-50 @enderror">
                    @error('business_type')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="business_age_months" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                        Usia Usaha (Bulan)
                    </label>
                    <input type="number"
                           name="business_age_months"
                           id="business_age_months"
                           value="{{ old('business_age_months') }}"
                           min="0"
                           placeholder="12"
                           class="input-base font-mono @error('business_age_months') border-red-500 bg-red-50 @enderror">
                    @error('business_age_months')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Modal Awal --}}
            <div>
                <label for="initial_capital" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Modal Awal (Rp)
                </label>
                <input type="number"
                       name="initial_capital"
                       id="initial_capital"
                       value="{{ old('initial_capital') }}"
                       min="0"
                       step="50000"
                       placeholder="5000000"
                       class="input-base font-mono @error('initial_capital') border-red-500 bg-red-50 @enderror">
                @error('initial_capital')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Produk / Jasa --}}
            <div>
                <label for="products_services" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Produk atau Layanan yang Disediakan
                </label>
                <textarea name="products_services"
                          id="products_services"
                          rows="2"
                          placeholder="Jelaskan produk utama atau jasa yang ditawarkan..."
                          class="input-base resize-none @error('products_services') border-red-500 bg-red-50 @enderror">{{ old('products_services') }}</textarea>
                @error('products_services')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kondisi Awal --}}
            <div>
                <label for="initial_condition" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Kondisi Awal Usaha (Baseline)
                </label>
                <textarea name="initial_condition"
                          id="initial_condition"
                          rows="2"
                          placeholder="Catatan kondisi tempat usaha, perputaran omset awal..."
                          class="input-base resize-none @error('initial_condition') border-red-500 bg-red-50 @enderror">{{ old('initial_condition') }}</textarea>
                @error('initial_condition')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat Usaha --}}
            <div>
                <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Lokasi / Alamat Tempat Usaha
                </label>
                <textarea name="address"
                          id="address"
                          rows="2"
                          placeholder="Alamat lengkap tempat usaha dijalankan..."
                          class="input-base resize-none @error('address') border-red-500 bg-red-50 @enderror">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="pt-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('businesses.index') }}" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    Simpan Usaha
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
