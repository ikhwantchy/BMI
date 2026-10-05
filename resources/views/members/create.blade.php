@extends('layouts.app')

@section('title', 'Tambah Anggota')
@section('page-title', 'Tambah Anggota Baru')

@section('content')
<div class="py-4 max-w-2xl">

    <div class="bg-white border border-gray-200 p-6">
        <div class="border-b border-gray-200 pb-4 mb-6">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Formulir Data Anggota Baru</h2>
            <p class="text-xs text-gray-500 mt-0.5">Lengkapi identitas anggota Koperasi BMI untuk pencatatan binaan</p>
        </div>

        <form method="POST" action="{{ route('members.store') }}" id="member-create-form" class="space-y-4">
            @csrf

            {{-- Nomor Anggota --}}
            <div>
                <label for="member_number" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Nomor Anggota <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="member_number"
                       name="member_number"
                       value="{{ old('member_number') }}"
                       placeholder="Contoh: BMI-2024-005"
                       class="input-base font-mono {{ $errors->has('member_number') ? 'border-red-400 bg-red-50' : '' }}">
                @error('member_number')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama Lengkap --}}
            <div>
                <label for="full_name" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="full_name"
                       name="full_name"
                       value="{{ old('full_name') }}"
                       placeholder="Nama lengkap sesuai KTP"
                       class="input-base {{ $errors->has('full_name') ? 'border-red-400 bg-red-50' : '' }}">
                @error('full_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div>
                <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Alamat Domisili <span class="text-red-500">*</span>
                </label>
                <textarea id="address"
                          name="address"
                          rows="3"
                          placeholder="Alamat lengkap tempat tinggal"
                          class="input-base resize-none {{ $errors->has('address') ? 'border-red-400 bg-red-50' : '' }}">{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Nomor Telepon / WhatsApp
                </label>
                <input type="tel"
                       id="phone"
                       name="phone"
                       value="{{ old('phone') }}"
                       placeholder="0812-3456-7890"
                       class="input-base font-mono">
            </div>

            {{-- Status --}}
            <div>
                <label for="membership_status" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Status Keanggotaan <span class="text-red-500">*</span>
                </label>
                <select id="membership_status"
                        name="membership_status"
                        class="input-base {{ $errors->has('membership_status') ? 'border-red-400 bg-red-50' : '' }}">
                    <option value="active" {{ old('membership_status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('membership_status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="suspended" {{ old('membership_status') === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                </select>
            </div>

            {{-- Catatan --}}
            <div>
                <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Catatan Khusus
                </label>
                <textarea id="notes"
                          name="notes"
                          rows="2"
                          placeholder="Catatan tambahan (opsional)"
                          class="input-base resize-none">{{ old('notes') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <button type="submit" class="btn-primary">
                    Simpan Anggota
                </button>
                <a href="{{ route('members.index') }}" class="btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
