@extends('layouts.app')

@section('title', 'Buat Dokumen Analisis Pembiayaan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Formulir Analisis Pembiayaan</h1>
            <p class="text-xs text-gray-500 mt-1">
                Isi atau verifikasi formulir analisis pembiayaan standar BMI. Data anggota dan usaha akan terisi otomatis.
            </p>
        </div>
        <a href="{{ route('documents.index') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
            &larr; Kembali ke Daftar Dokumen
        </a>
    </div>

    {{-- Member Selector / Auto-populate trigger --}}
    <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg">
        <label for="member-select" class="block text-xs font-bold text-emerald-900 uppercase tracking-wider mb-1.5">
            Pilih Anggota Koperasi (Auto-Populate Data)
        </label>
        <div class="flex items-center gap-3">
            <select id="member-select"
                    onchange="if(this.value) window.location.href = '{{ route('documents.financing-analysis.create') }}?member_id=' + this.value"
                    class="flex-1 text-xs border border-emerald-300 rounded px-3 py-2 bg-white text-gray-800">
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
    <form method="POST" action="{{ route('documents.financing-analysis.store') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="member_id" value="{{ $selectedMember->id }}">

        {{-- A. DATA ANGGOTA --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm space-y-4">
            <h2 class="text-xs font-bold text-[#009a4c] uppercase tracking-wider border-b border-gray-100 pb-2">
                A. Data Anggota (Terisi Otomatis)
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Anggota</label>
                    <input type="text" value="{{ $selectedMember->full_name }}" disabled class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-gray-600">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nomor Anggota</label>
                    <input type="text" value="{{ $selectedMember->member_number }}" disabled class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-gray-600">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Suami / Istri</label>
                    <input type="text" value="{{ $selectedMember->spouse_name ?? '-' }}" disabled class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-gray-600">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Rembug Pusat (RP)</label>
                    <input type="text" value="{{ $selectedMember->rembug_pusat ?? '-' }}" disabled class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-gray-600">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Tanggal Analisis</label>
                    <input type="date" name="analysis_date" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Pilih Unit Usaha Binaan</label>
                    <select name="business_id" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                        @foreach ($selectedMember->businesses as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->business_type }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- B. PEMBIAYAAN --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm space-y-4">
            <h2 class="text-xs font-bold text-[#009a4c] uppercase tracking-wider border-b border-gray-100 pb-2">
                B. Data Pembiayaan
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Rencana Pengajuan (Rp)*</label>
                    <input type="number" name="proposed_amount" value="15000000" required class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Plafon Pembiayaan Disetujui (Rp)</label>
                    <input type="number" name="approved_ceiling" value="15000000" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Pembiayaan Terakhir di BMI (Rp)</label>
                    <input type="number" name="last_financing_amount" value="10000000" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Plafon Pembiayaan Investasi (Rp)</label>
                    <input type="number" name="investment_ceiling" value="0" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-gray-700 mb-1">Tujuan Penggunaan Pembiayaan</label>
                    <textarea name="financing_purpose" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800" placeholder="Penambahan barang dagangan, modal kerja...">Penambahan stok barang dagangan dan modal kerja operasional</textarea>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Pembiayaan di Tempat Lain (Rp)</label>
                    <input type="number" name="financing_other_institution" value="0" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
            </div>
        </div>

        {{-- C. KAPASITAS USAHA & ASET --}}
        @php
            $biz = $selectedMember->businesses->first();
        @endphp
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm space-y-4">
            <h2 class="text-xs font-bold text-[#009a4c] uppercase tracking-wider border-b border-gray-100 pb-2">
                C & D. Kapasitas Usaha, Aset dan Simpanan
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Jenis Usaha</label>
                    <input type="text" name="business_type" value="{{ $biz?->business_type ?? 'Perdagangan' }}" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Omset Bulanan (Rp)</label>
                    <input type="number" id="in_turnover" name="monthly_turnover" value="{{ $biz?->monthly_turnover ?: 30000000 }}" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Omset Harian (Rp)</label>
                    <input type="number" name="daily_turnover" value="{{ $biz?->daily_turnover ?: 1000000 }}" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Aset Usaha & Tempat (Rp)</label>
                    <input type="number" name="business_assets_estimate" value="25000000" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Simpanan di BMI (Rp)</label>
                    <input type="number" name="savings_amount" value="3500000" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Aset Kendaraan (Rp)</label>
                    <input type="number" name="vehicle_assets_estimate" value="18000000" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
            </div>
        </div>

        {{-- E & F. KEUANGAN & KALKULASI REPAYMENT --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm space-y-4">
            <h2 class="text-xs font-bold text-[#009a4c] uppercase tracking-wider border-b border-gray-100 pb-2">
                E & F. Arus Kas Bulanan & Kapasitas Mengangsur (Live Calculation)
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Pendapatan Bersih Usaha (Rp)*</label>
                    <input type="number" id="in_biz_income" name="business_income" value="5500000" oninput="recalc()" required class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Pendapatan Pasangan (Rp)</label>
                    <input type="number" id="in_spouse_income" name="spouse_income" value="{{ $selectedMember->spouse_income ?: 2500000 }}" oninput="recalc()" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Pengeluaran Rumah Tangga (Rp)*</label>
                    <input type="number" id="in_hh_exp" name="household_expenses" value="3500000" oninput="recalc()" required class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Biaya Operasional Usaha (Rp)*</label>
                    <input type="number" id="in_biz_exp" name="business_expenses" value="1000000" oninput="recalc()" required class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Angsuran Lembaga Lain (Rp)</label>
                    <input type="number" id="in_other_inst" name="other_installments" value="0" oninput="recalc()" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Kesimpulan Kelayakan</label>
                    <select name="conclusion" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800 font-bold">
                        <option value="layak">LAYAK</option>
                        <option value="layak_bersyarat">LAYAK BERSYARAT</option>
                        <option value="tidak_layak">TIDAK LAYAK</option>
                    </select>
                </div>
            </div>

            {{-- Live Results Banner --}}
            <div class="mt-4 p-4 bg-[#f0f9f4] border border-[#a7e0be] rounded-lg grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-[11px] text-gray-500 uppercase font-semibold">Total Pendapatan</div>
                    <div id="disp_tot_inc" class="text-sm font-bold text-gray-900 mt-0.5">Rp 8.000.000</div>
                </div>
                <div>
                    <div class="text-[11px] text-gray-500 uppercase font-semibold">Saving Capacity</div>
                    <div id="disp_sav_cap" class="text-sm font-bold text-emerald-700 mt-0.5">Rp 3.500.000</div>
                </div>
                <div>
                    <div class="text-[11px] text-gray-500 uppercase font-semibold">Kemampuan Mengangsur (75%)</div>
                    <div id="disp_inst_cap" class="text-base font-extrabold text-[#009a4c] mt-0.5">Rp 2.625.000</div>
                </div>
            </div>

            <div class="text-xs">
                <label class="block font-semibold text-gray-700 mb-1">Catatan Analis</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800">Arus kas stabil, perputaran barang cepat dan memiliki kapasitas menabung yang memadai.</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('documents.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-xs font-semibold rounded hover:bg-gray-300">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#009a4c] hover:bg-[#007d3e] text-white text-xs font-bold uppercase tracking-wider rounded shadow cursor-pointer">
                Simpan & Terbitkan Formulir Analisis
            </button>
        </div>
    </form>

    <script>
        function recalc() {
            const bizInc = parseFloat(document.getElementById('in_biz_income').value) || 0;
            const spouseInc = parseFloat(document.getElementById('in_spouse_income').value) || 0;
            const hhExp = parseFloat(document.getElementById('in_hh_exp').value) || 0;
            const bizExp = parseFloat(document.getElementById('in_biz_exp').value) || 0;
            const otherInst = parseFloat(document.getElementById('in_other_inst').value) || 0;

            const totInc = bizInc + spouseInc;
            const totExp = hhExp + bizExp + otherInst;
            const savCap = totInc - totExp;
            const instCap = savCap > 0 ? Math.round(savCap * 0.75) : 0;

            document.getElementById('disp_tot_inc').innerText = 'Rp ' + totInc.toLocaleString('id-ID');
            document.getElementById('disp_sav_cap').innerText = 'Rp ' + savCap.toLocaleString('id-ID');
            document.getElementById('disp_inst_cap').innerText = 'Rp ' + instCap.toLocaleString('id-ID');
        }
        recalc();
    </script>
    @endif
</div>
@endsection
