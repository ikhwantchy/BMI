@extends('layouts.app')

@section('title', 'Manajemen Cabang')
@section('page-title', 'Kelola Kantor Cabang Kopsyah BMI')

@section('content')
<div class="py-4 space-y-6">

    {{-- Header --}}
    <div class="border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900 tracking-tight">Daftar Kantor Cabang</h2>
            <p class="text-xs text-gray-500 mt-0.5">Struktur organisasi dan batasan data operasional wilayah kerja</p>
        </div>
        @if(auth()->user()->canManageBranches())
            <button type="button" onclick="document.getElementById('modal-add-branch').classList.remove('hidden')"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#009a4c] text-white text-xs font-semibold hover:bg-[#007d3e] transition-colors shadow-sm cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Cabang
            </button>
        @endif
    </div>

    {{-- Branches Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($branches as $b)
            <div class="bg-white border border-gray-200 p-5 space-y-3 relative group">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[10px] font-mono uppercase px-2 py-0.5 bg-gray-100 text-gray-700 border border-gray-200 font-bold">
                            {{ $b->code }}
                        </span>
                        <h3 class="text-sm font-bold text-gray-900 mt-2">{{ $b->name }}</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500" title="Aktif"></span>
                        @if(auth()->user()->canManageBranches())
                            <button type="button" onclick="editBranch({{ json_encode($b) }})"
                                    class="text-indigo-600 hover:text-indigo-900 text-xs font-medium cursor-pointer">
                                Edit
                            </button>
                        @endif
                    </div>
                </div>

                <p class="text-xs text-gray-500 line-clamp-2">
                    {{ $b->address ?? 'Alamat belum dilengkapi.' }}
                </p>

                <div class="pt-3 border-t border-gray-100 grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="bg-gray-50 p-2">
                        <span class="text-[10px] text-gray-400 block uppercase font-medium">Staf</span>
                        <span class="font-mono font-bold text-gray-900">{{ $b->users_count }}</span>
                    </div>
                    <div class="bg-gray-50 p-2">
                        <span class="text-[10px] text-gray-400 block uppercase font-medium">Anggota</span>
                        <span class="font-mono font-bold text-gray-900">{{ $b->members_count }}</span>
                    </div>
                    <div class="bg-gray-50 p-2">
                        <span class="text-[10px] text-gray-400 block uppercase font-medium">Usaha</span>
                        <span class="font-mono font-bold text-gray-900">{{ $b->businesses_count }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white border border-gray-200 p-8 text-center text-gray-400 italic">
                Belum ada data cabang.
            </div>
        @endforelse
    </div>

</div>

{{-- Modal Tambah Cabang --}}
<div id="modal-add-branch" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white border border-gray-200 max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="font-bold text-gray-900 text-sm">Tambah Kantor Cabang Baru</h3>
            <button onclick="document.getElementById('modal-add-branch').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>

        <form action="{{ route('branches.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Cabang (misal: B02) *</label>
                <input type="text" name="code" required placeholder="Contoh: B02"
                       class="w-full text-xs border border-gray-300 px-3 py-2 uppercase font-mono focus:border-[#006633] focus:ring-0">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Cabang *</label>
                <input type="text" name="name" required placeholder="Contoh: Cabang Tangerang Barat"
                       class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Kantor</label>
                <textarea name="address" rows="3" placeholder="Alamat lengkap kantor cabang"
                          class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-add-branch').classList.add('hidden')" class="px-3 py-1.5 border border-gray-300 text-xs text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-1.5 bg-[#009a4c] text-white text-xs font-semibold hover:bg-[#007d3e]">Simpan Cabang</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Cabang --}}
<div id="modal-edit-branch" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white border border-gray-200 max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="font-bold text-gray-900 text-sm">Edit Data Kantor Cabang</h3>
            <button onclick="document.getElementById('modal-edit-branch').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>

        <form id="form-edit-branch" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Cabang *</label>
                <input type="text" id="edit-branch-code" name="code" required
                       class="w-full text-xs border border-gray-300 px-3 py-2 uppercase font-mono focus:border-[#006633] focus:ring-0">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Cabang *</label>
                <input type="text" id="edit-branch-name" name="name" required
                       class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Kantor</label>
                <textarea id="edit-branch-address" name="address" rows="3"
                          class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-edit-branch').classList.add('hidden')" class="px-3 py-1.5 border border-gray-300 text-xs text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-1.5 bg-[#009a4c] text-white text-xs font-semibold hover:bg-[#007d3e]">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editBranch(b) {
    document.getElementById('form-edit-branch').action = `/branches/${b.id}`;
    document.getElementById('edit-branch-code').value = b.code;
    document.getElementById('edit-branch-name').value = b.name;
    document.getElementById('edit-branch-address').value = b.address || '';
    document.getElementById('modal-edit-branch').classList.remove('hidden');
}
</script>
@endsection
