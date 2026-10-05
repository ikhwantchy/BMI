@extends('layouts.app')

@section('title', 'Edit Anggota: ' . $member->full_name)
@section('page-title', 'Edit Data Anggota')

@section('content')
<div class="py-4 max-w-2xl">

    <div class="bg-white border border-gray-200 p-6">
        <div class="border-b border-gray-200 pb-4 mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Perbarui Data Anggota</h3>
                <p class="text-xs text-gray-500 mt-0.5">Sesuaikan informasi identitas dan status keanggotaan</p>
            </div>
            <a href="{{ route('members.show', $member) }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('members.update', $member) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Nomor Anggota --}}
            <div>
                <label for="member_number" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Nomor Anggota <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="member_number"
                       id="member_number"
                       value="{{ old('member_number', $member->member_number) }}"
                       required
                       class="input-base font-mono @error('member_number') border-red-500 bg-red-50 @enderror">
                @error('member_number')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama Lengkap --}}
            <div>
                <label for="full_name" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="full_name"
                       id="full_name"
                       value="{{ old('full_name', $member->full_name) }}"
                       required
                       class="input-base @error('full_name') border-red-500 bg-red-50 @enderror">
                @error('full_name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="membership_status" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Status Keanggotaan <span class="text-red-500">*</span>
                </label>
                <select name="membership_status"
                        id="membership_status"
                        required
                        class="input-base @error('membership_status') border-red-500 bg-red-50 @enderror">
                    <option value="active" {{ old('membership_status', $member->membership_status->value) === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('membership_status', $member->membership_status->value) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="suspended" {{ old('membership_status', $member->membership_status->value) === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                </select>
                @error('membership_status')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Nomor Telepon / WhatsApp
                </label>
                <input type="tel"
                       name="phone"
                       id="phone"
                       value="{{ old('phone', $member->phone) }}"
                       placeholder="0812-3456-7890"
                       class="input-base font-mono">
            </div>

            {{-- Alamat --}}
            <div>
                <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Alamat Lengkap <span class="text-red-500">*</span>
                </label>
                <textarea name="address"
                          id="address"
                          rows="3"
                          required
                          class="input-base resize-none @error('address') border-red-500 bg-red-50 @enderror">{{ old('address', $member->address) }}</textarea>
                @error('address')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Catatan --}}
            <div>
                <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Catatan Tambahan
                </label>
                <textarea name="notes"
                          id="notes"
                          rows="2"
                          class="input-base resize-none @error('notes') border-red-500 bg-red-50 @enderror">{{ old('notes', $member->notes) }}</textarea>
                @error('notes')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="pt-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('members.show', $member) }}" class="btn-secondary">
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
