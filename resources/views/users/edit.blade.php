@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Data Pengguna')

@section('content')
<div class="py-4 max-w-2xl">
    <div class="bg-white border border-gray-200 p-6 space-y-6">
        <div class="border-b border-gray-100 pb-3">
            <h3 class="font-semibold text-gray-900 text-sm">Edit Data: {{ $user->name }}</h3>
            <p class="text-xs text-gray-500 mt-0.5">Perbarui wewenang peran, cabang, dan status akun</p>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0">
                    @error('name') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Username *</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                           class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0">
                    @error('username') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0">
                    @error('email') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Ganti Password (Opsional)</label>
                    <input type="password" name="password" minlength="8" placeholder="Kosongkan jika tidak diubah"
                           class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0">
                    @error('password') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Role / Peran *</label>
                    <select name="role" required class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0">
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ old('role', $user->role) === $r->name ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Cabang Operasional</label>
                    <select name="branch_id" class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0">
                        <option value="">(Pusat / Semua Cabang)</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id', $user->branch_id) == $b->id ? 'selected' : '' }}>
                                {{ $b->code }} — {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status Akun *</label>
                    <select name="status" required class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0">
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 text-xs font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-[#006633] text-white text-xs font-semibold hover:bg-[#00552b]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
