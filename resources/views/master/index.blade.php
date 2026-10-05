@extends('layouts.app')

@section('title', 'Master Data')
@section('page-title', 'Kelola Master Data Operasional')

@section('content')
<div class="py-4 space-y-6">

    {{-- Header --}}
    <div class="border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-base font-semibold text-gray-900 tracking-tight">Master Data Parameter & Referensi</h2>
            <p class="text-xs text-gray-500 mt-0.5">Daftar referensi baku untuk jenis usaha, kategori kendala, dan program pembinaan</p>
        </div>
        @if(auth()->user()->checkRole(['system_admin', 'pengurus', 'manajer']))
            <button onclick="document.getElementById('modal-add-master').classList.remove('hidden')"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#009a4c] text-white text-xs font-semibold hover:bg-[#007d3e] transition-colors shadow-sm cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Item Master
            </button>
        @endif
    </div>

    {{-- Category Tabs --}}
    <div class="flex border-b border-gray-200 gap-2">
        @foreach($categories as $catKey => $catLabel)
            <a href="{{ route('master.index', ['category' => $catKey]) }}"
               class="px-4 py-2 text-xs font-medium border-b-2 transition-colors {{ $selectedCategory === $catKey ? 'border-[#006633] text-[#006633] font-bold bg-emerald-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                {{ $catLabel }}
            </a>
        @endforeach
    </div>

    {{-- Items Table --}}
    <div class="bg-white border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Nama Referensi</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Deskripsi</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider text-[11px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap font-mono font-bold text-gray-700">
                                {{ $item->code }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">
                                {{ $item->name }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate">
                                {{ $item->description ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($item->is_active)
                                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-700 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] text-gray-400 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                @if(auth()->user()->checkRole(['system_admin', 'pengurus', 'manajer']))
                                    <button onclick="editMaster({{ json_encode($item) }})"
                                            class="text-indigo-600 hover:text-indigo-900 font-medium cursor-pointer">
                                        Edit
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 italic">
                                Belum ada data untuk kategori ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Modal Tambah Master Data --}}
<div id="modal-add-master" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white border border-gray-200 max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="font-bold text-gray-900 text-sm">Tambah Item Master Data</h3>
            <button onclick="document.getElementById('modal-add-master').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>

        <form action="{{ route('master.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="category" value="{{ $selectedCategory }}">

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                <input type="text" value="{{ $categories[$selectedCategory] ?? $selectedCategory }}" disabled
                       class="w-full text-xs border border-gray-200 bg-gray-50 px-3 py-2 text-gray-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Unik (Huruf/Angka/Garis Bawah) *</label>
                <input type="text" name="code" required placeholder="Contoh: KULINER_KATERING"
                       class="w-full text-xs border border-gray-300 px-3 py-2 uppercase font-mono focus:border-[#006633] focus:ring-0">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Tampilan *</label>
                <input type="text" name="name" required placeholder="Contoh: Usaha Katering & Nasi Box"
                       class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi Tambahan</label>
                <textarea name="description" rows="2" class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-add-master').classList.add('hidden')" class="px-3 py-1.5 border border-gray-300 text-xs text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-1.5 bg-[#006633] text-white text-xs font-semibold hover:bg-[#00552b]">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Master Data --}}
<div id="modal-edit-master" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white border border-gray-200 max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="font-bold text-gray-900 text-sm">Edit Item Master Data</h3>
            <button onclick="document.getElementById('modal-edit-master').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>

        <form id="form-edit-master" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Unik *</label>
                <input type="text" id="edit-code" name="code" required
                       class="w-full text-xs border border-gray-300 px-3 py-2 uppercase font-mono focus:border-[#006633] focus:ring-0">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Tampilan *</label>
                <input type="text" id="edit-name" name="name" required
                       class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi Tambahan</label>
                <textarea id="edit-description" name="description" rows="2" class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status Keaktifan *</label>
                <select id="edit-is-active" name="is_active" class="w-full text-xs border border-gray-300 px-3 py-2 focus:border-[#006633] focus:ring-0">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-edit-master').classList.add('hidden')" class="px-3 py-1.5 border border-gray-300 text-xs text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-1.5 bg-[#006633] text-white text-xs font-semibold hover:bg-[#00552b]">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editMaster(item) {
    document.getElementById('form-edit-master').action = `/master-data/${item.id}`;
    document.getElementById('edit-code').value = item.code;
    document.getElementById('edit-name').value = item.name;
    document.getElementById('edit-description').value = item.description || '';
    document.getElementById('edit-is-active').value = item.is_active ? '1' : '0';
    document.getElementById('modal-edit-master').classList.remove('hidden');
}
</script>
@endsection
