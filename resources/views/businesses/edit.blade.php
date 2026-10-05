@extends('layouts.app')

@section('title', 'Edit Usaha: ' . $business->name)
@section('page-title', 'Edit Usaha')

@section('content')
<div class="py-4 max-w-2xl">

    <div class="bg-white border border-gray-200 p-6">
        <div class="border-b border-gray-200 pb-4 mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Edit Data Usaha</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui profil unit usaha anggota</p>
            </div>
            <a href="{{ route('businesses.show', $business) }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('businesses.update', $business) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Info Anggota Pemilik (Readonly) --}}
            <div class="bg-gray-50 p-3.5 border border-gray-200">
                <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider block">Pemilik Usaha (Anggota)</span>
                <span class="text-xs font-semibold text-gray-900">{{ $business->member->full_name }} ({{ $business->member->member_number }})</span>
            </div>

            {{-- Nama Usaha --}}
            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Nama Usaha <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name', $business->name) }}"
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
                           value="{{ old('business_type', $business->business_type) }}"
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
                           value="{{ old('business_age_months', $business->business_age_months) }}"
                           min="0"
                           class="input-base font-mono @error('business_age_months') border-red-500 bg-red-50 @enderror">
                    @error('business_age_months')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Modal Awal & Status --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="initial_capital" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                        Modal Awal (Rp)
                    </label>
                    <input type="number"
                           name="initial_capital"
                           id="initial_capital"
                           value="{{ old('initial_capital', $business->initial_capital) }}"
                           min="0"
                           step="50000"
                           class="input-base font-mono @error('initial_capital') border-red-500 bg-red-50 @enderror">
                    @error('initial_capital')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                        Status Usaha <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                            id="status"
                            required
                            class="input-base @error('status') border-red-500 bg-red-50 @enderror">
                        <option value="active" {{ old('status', $business->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $business->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Produk / Jasa --}}
            <div>
                <label for="products_services" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Produk atau Layanan
                </label>
                <textarea name="products_services"
                          id="products_services"
                          rows="2"
                          class="input-base resize-none @error('products_services') border-red-500 bg-red-50 @enderror">{{ old('products_services', $business->products_services) }}</textarea>
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
                          class="input-base resize-none @error('initial_condition') border-red-500 bg-red-50 @enderror">{{ old('initial_condition', $business->initial_condition) }}</textarea>
                @error('initial_condition')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div>
                <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Alamat Lengkap Usaha
                </label>
                <textarea name="address"
                          id="address"
                          rows="2"
                          class="input-base resize-none @error('address') border-red-500 bg-red-50 @enderror">{{ old('address', $business->address) }}</textarea>
                @error('address')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="pt-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('businesses.show', $business) }}" class="btn-secondary">
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
