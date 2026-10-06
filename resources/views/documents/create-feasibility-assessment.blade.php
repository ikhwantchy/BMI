@extends('layouts.app')

@section('title', 'Buat Dokumen Uji Kelayakan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Formulir Uji Kelayakan Anggota</h1>
            <p class="text-xs text-gray-500 mt-1">
                Survei lapangan dan verifikasi kelayakan sosial, rumah tangga, serta karakter calon anggota BMI.
            </p>
        </div>
        <a href="{{ route('documents.index') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
            &larr; Kembali ke Daftar Dokumen
        </a>
    </div>

    {{-- Member Selector / Auto-populate trigger --}}
    <div class="bg-teal-50 border border-teal-200 p-4 rounded-lg">
        <label for="member-select" class="block text-xs font-bold text-teal-900 uppercase tracking-wider mb-1.5">
            Pilih Anggota Koperasi (Auto-Populate Data)
        </label>
        <div class="flex items-center gap-3">
            <select id="member-select"
                    onchange="if(this.value) window.location.href = '{{ route('documents.feasibility-assessment.create') }}?member_id=' + this.value"
                    class="flex-1 text-xs border border-teal-300 rounded px-3 py-2 bg-white text-gray-800">
                <option value="">-- Pilih Anggota untuk Isi Otomatis --</option>
                @foreach ($members as $m)
                    <option value="{{ $m->id }}" {{ ($selectedMember?->id === $m->id) ? 'selected' : '' }}>
                        {{ $m->member_number }} - {{ $m->full_name }} ({{ $m->rembug_pusat ?? 'Kantor Pusat' }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($selectedMember)
    <form method="POST" action="{{ route('documents.feasibility-assessment.store') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="member_id" value="{{ $selectedMember->id }}">

        {{-- A. DATA ANGGOTA --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm space-y-4">
            <h2 class="text-xs font-bold text-teal-800 uppercase tracking-wider border-b border-gray-100 pb-2">
                A. Data Anggota (Terverifikasi)
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Anggota (KTP)</label>
                    <input type="text" value="{{ $selectedMember->full_name }}" disabled class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-gray-600">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nomor KTP / NIK*</label>
                    <input type="text" name="nik" value="{{ $selectedMember->nik ?? '321601' . rand(1000000000, 9999999999) }}" required class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Tanggal Survei</label>
                    <input type="date" name="assessment_date" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Tempat / Tgl Lahir</label>
                    <input type="text" name="birth_place_date" value="{{ $selectedMember->birth_place_date ?? 'Tangerang, 12 Mei 1985' }}" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Status Perkawinan</label>
                    <select name="marital_status" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                        <option value="Menikah" {{ ($selectedMember->marital_status ?? 'Menikah') === 'Menikah' ? 'selected' : '' }}>Menikah</option>
                        <option value="Belum Menikah">Belum Menikah</option>
                        <option value="Janda / Duda">Janda / Duda</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Pendidikan Terakhir</label>
                    <input type="text" name="education" value="{{ $selectedMember->education ?? 'SMA' }}" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">RT / RW</label>
                    <input type="text" name="rt_rw" value="003 / 002" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Desa / Kelurahan</label>
                    <input type="text" name="village" value="Pasir Kaliki" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Kecamatan</label>
                    <input type="text" name="district" value="Curug" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
            </div>
        </div>

        {{-- B. DATA PASANGAN --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm space-y-4">
            <h2 class="text-xs font-bold text-teal-800 uppercase tracking-wider border-b border-gray-100 pb-2">
                B. Data Pasangan
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Pasangan (Suami/Istri)</label>
                    <input type="text" name="spouse_name" value="{{ $selectedMember->spouse_name ?? 'Usman Supardi' }}" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">NIK Pasangan</label>
                    <input type="text" name="spouse_nik" value="{{ $selectedMember->spouse_nik ?? '3216011208790002' }}" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Pekerjaan Pasangan</label>
                    <input type="text" name="spouse_occupation" value="{{ $selectedMember->spouse_occupation ?? 'Wiraswasta' }}" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Penghasilan Pasangan (Rp/bln)</label>
                    <input type="number" name="spouse_income" value="{{ $selectedMember->spouse_income ?: 3500000 }}" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">No. HP Pasangan</label>
                    <input type="text" name="spouse_phone" value="{{ $selectedMember->spouse_phone ?? '081299887766' }}" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
            </div>
        </div>

        {{-- C & D. KELUARGA, RUMAH TINGGAL & ASET --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm space-y-4">
            <h2 class="text-xs font-bold text-teal-800 uppercase tracking-wider border-b border-gray-100 pb-2">
                C & D. Kondisi Rumah Tinggal & Aset Rumah Tangga
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Jumlah Tanggungan Keluarga</label>
                    <input type="number" name="dependents_count" value="{{ $selectedMember->dependents_count ?: 3 }}" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Anak Masih Sekolah</label>
                    <input type="number" name="schooling_children_count" value="2" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Status Tempat Tinggal</label>
                    <input type="text" name="home_ownership_status" value="Milik Sendiri (SHM)" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Kondisi Dinding</label>
                    <input type="text" name="wall_type" value="Tembok Permanen" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Kondisi Lantai</label>
                    <input type="text" name="floor_type" value="Keramik" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Kondisi Atap</label>
                    <input type="text" name="roof_type" value="Genteng" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Sumber Air</label>
                    <input type="text" name="water_source" value="Sumur Bor / Pompa Listrik" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Daya Listrik</label>
                    <input type="text" name="electricity_power" value="PLN 1300 VA" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div class="sm:col-span-3">
                    <label class="block font-semibold text-gray-700 mb-1">Aset Kendaraan & Elektronik</label>
                    <input type="text" name="vehicle_assets" value="2 Unit Motor (Honda Vario & Beat), Kulkas 2 Pintu, TV LED" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
            </div>
        </div>

        {{-- E & F. KARAKTER & KESIMPULAN --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm space-y-4">
            <h2 class="text-xs font-bold text-teal-800 uppercase tracking-wider border-b border-gray-100 pb-2">
                E & F. Karakter, Hubungan Sosial & Hasil Rekomendasi
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Hubungan Sosial dengan Warga</label>
                    <input type="text" name="community_relation" value="Sangat Baik & Rukun" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Keaktifan di Rembug Pusat (RP)</label>
                    <input type="text" name="rembug_pusat_activity" value="Aktif dan Rajin Mengikuti Pertemuan" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-gray-700 mb-1">Reputasi & Karakter Anggota</label>
                    <input type="text" name="reputation_character" value="Amanah, dapat dipercaya, berpenampilan rapi dan santun dalam bermuamalah." class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-gray-700 mb-1">Hasil Uji Kelayakan</label>
                    <select name="result" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800 font-bold">
                        <option value="memenuhi_syarat">MEMENUHI SYARAT (LAYAK)</option>
                        <option value="perlu_pertimbangan">PERLU PERTIMBANGAN</option>
                        <option value="tidak_memenuhi_syarat">TIDAK MEMENUHI SYARAT</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-gray-700 mb-1">Catatan Verifikator Lapangan</label>
                    <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">Kondisi rumah permanen, keluarga rukun, dan reputasi di lingkungan sekitar sangat positif.</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('documents.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-xs font-semibold rounded hover:bg-gray-300">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold uppercase tracking-wider rounded shadow cursor-pointer">
                Simpan & Terbitkan Formulir Uji Kelayakan
            </button>
        </div>
    </form>
    @endif
</div>
@endsection
