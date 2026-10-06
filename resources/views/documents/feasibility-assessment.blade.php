<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Uji Kelayakan - {{ $assessment->assessment_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border-bottom: 2px solid #009a4c;
            padding-bottom: 6px;
        }
        .header-logo {
            width: 70px;
            text-align: left;
            vertical-align: middle;
        }
        .header-text {
            text-align: center;
            vertical-align: middle;
        }
        .header-title {
            font-size: 13pt;
            font-weight: bold;
            color: #009a4c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .header-subtitle {
            font-size: 10.5pt;
            font-weight: bold;
            color: #333;
            margin: 2px 0 0 0;
        }
        .header-meta {
            font-size: 8pt;
            color: #555;
            margin-top: 2px;
        }
        .meta-bar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            background: #f7faf7;
            border: 1px solid #d4e5d4;
        }
        .meta-bar td {
            padding: 4px 8px;
            font-size: 8.5pt;
        }
        .section-header {
            background-color: #009a4c;
            color: #ffffff;
            font-weight: bold;
            font-size: 9pt;
            padding: 4px 8px;
            text-transform: uppercase;
            margin-top: 8px;
            margin-bottom: 4px;
            letter-spacing: 0.3px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cfcfcf;
            padding: 4px 6px;
            vertical-align: top;
            font-size: 8.5pt;
        }
        .data-table th {
            background-color: #f0f4f0;
            font-weight: bold;
            text-align: left;
        }
        .label-col {
            width: 28%;
            background-color: #fafafa;
            font-weight: 600;
            color: #333;
        }
        .val-col {
            width: 22%;
        }
        .full-val {
            width: 72%;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 2px;
        }
        .badge-layak {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-bersyarat {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .badge-tidak {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            page-break-inside: avoid;
        }
        .signatures-table td {
            border: 1px solid #bbb;
            padding: 6px;
            text-align: center;
            vertical-align: top;
            width: 33.33%;
            font-size: 8pt;
        }
        .sig-role {
            font-weight: bold;
            color: #333;
            margin-bottom: 45px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .doc-footer {
            margin-top: 10px;
            font-size: 7.5pt;
            color: #777;
            text-align: right;
            border-top: 1px dashed #ccc;
            padding-top: 3px;
        }
        .no-print {
            margin-bottom: 15px;
            padding: 8px 12px;
            background: #e6fffa;
            border: 1px solid #81e6d9;
            text-align: right;
        }
        .btn-print {
            background: #009a4c;
            color: white;
            padding: 6px 14px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 3px;
            display: inline-block;
            font-size: 9pt;
            cursor: pointer;
            border: none;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    @if (!isset($isPdf) || !$isPdf)
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">Cetak Dokumen (Print / PDF)</button>
        <a href="{{ route('documents.feasibility-assessment.pdf', $assessment->id) }}" class="btn-print" style="background:#007d3e; margin-left:6px;">Download PDF</a>
        <a href="{{ url()->previous() }}" class="btn-print" style="background:#666; margin-left:6px;">Kembali</a>
    </div>
    @endif

    {{-- HEADER RESMI KOPERASI SYARIAH BMI --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ public_path('images/logo-kopsyah-bmi-new.png') }}"
                     alt="Logo BMI"
                     style="height: 48px;"
                     onerror="this.style.display='none'">
            </td>
            <td class="header-text">
                <div class="header-title">Koperasi Konsumen Syariah Benteng Mikro Indonesia</div>
                <div class="header-subtitle">FORMULIR UJI KELAYAKAN ANGGOTA & KELUARGA</div>
                <div class="header-meta">Survei Lapangan Sosial, Karakter, dan Rumah Tangga Anggota</div>
            </td>
        </tr>
    </table>

    {{-- METADATA BAR --}}
    <table class="meta-bar">
        <tr>
            <td style="width: 25%;"><strong>No. Dokumen:</strong> {{ $assessment->assessment_number }}</td>
            <td style="width: 25%;"><strong>Kantor Cabang:</strong> {{ $assessment->branch?->name ?? $assessment->member?->branch?->name ?? 'Cabang Pusat' }}</td>
            <td style="width: 25%;"><strong>Rembug Pusat:</strong> {{ $assessment->member?->rembug_pusat ?? '-' }}</td>
            <td style="width: 25%; text-align: right;"><strong>Tanggal Survei:</strong> {{ $assessment->assessment_date?->format('d/m/Y') ?? date('d/m/Y') }}</td>
        </tr>
    </table>

    {{-- A. DATA ANGGOTA --}}
    <div class="section-header">A. DATA ANGGOTA</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Nama Lengkap (KTP)</td>
            <td class="val-col"><strong>{{ $assessment->member?->full_name }}</strong></td>
            <td class="label-col">Nomor Anggota</td>
            <td class="val-col"><strong>{{ $assessment->member?->member_number }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">Nomor KTP / NIK</td>
            <td class="val-col">{{ $assessment->nik ?? $assessment->member?->nik ?? '-' }}</td>
            <td class="label-col">Tempat / Tgl Lahir</td>
            <td class="val-col">{{ $assessment->birth_place_date ?? $assessment->member?->birth_place_date ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Status Perkawinan</td>
            <td class="val-col">{{ $assessment->marital_status ?? $assessment->member?->marital_status ?? 'Menikah' }}</td>
            <td class="label-col">Pendidikan Terakhir</td>
            <td class="val-col">{{ $assessment->education ?? $assessment->member?->education ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat Domisili</td>
            <td class="full-val" colspan="3">{{ $assessment->member?->address ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">RT / RW & Desa</td>
            <td class="val-col">RT/RW: {{ $assessment->rt_rw ?? '-' }} &bull; Desa: {{ $assessment->village ?? '-' }}</td>
            <td class="label-col">Kecamatan & No. Telepon</td>
            <td class="val-col">Kec: {{ $assessment->district ?? '-' }} &bull; HP: {{ $assessment->member?->phone ?? '-' }}</td>
        </tr>
    </table>

    {{-- B. DATA PASANGAN --}}
    <div class="section-header">B. DATA PASANGAN</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Nama Suami / Istri</td>
            <td class="val-col"><strong>{{ $assessment->spouse_name ?? $assessment->member?->spouse_name ?? '-' }}</strong></td>
            <td class="label-col">Nomor NIK Pasangan</td>
            <td class="val-col">{{ $assessment->spouse_nik ?? $assessment->member?->spouse_nik ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Pekerjaan Pasangan</td>
            <td class="val-col">{{ $assessment->spouse_occupation ?? $assessment->member?->spouse_occupation ?? '-' }}</td>
            <td class="label-col">Penghasilan Pasangan</td>
            <td class="val-col" style="font-weight: 600; color: #007d3e;">
                Rp {{ number_format($assessment->spouse_income ?: ($assessment->member?->spouse_income ?? 0), 0, ',', '.') }} / bulan
            </td>
        </tr>
        <tr>
            <td class="label-col">No. Telepon / HP Pasangan</td>
            <td class="full-val" colspan="3">{{ $assessment->spouse_phone ?? $assessment->member?->spouse_phone ?? '-' }}</td>
        </tr>
    </table>

    {{-- C. DATA ANGGOTA KELUARGA & KONDISI TEMPAT TINGGAL --}}
    <div class="section-header">C. DATA ANGGOTA KELUARGA & KONDISI TEMPAT TINGGAL</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Jumlah Tanggungan Keluarga</td>
            <td class="val-col"><strong>{{ $assessment->dependents_count ?: ($assessment->member?->dependents_count ?? 0) }} Orang</strong></td>
            <td class="label-col">Anak yang Masih Sekolah</td>
            <td class="val-col">{{ $assessment->schooling_children_count }} Anak</td>
        </tr>
        <tr>
            <td class="label-col">Status Kepemilikan Rumah</td>
            <td class="val-col">{{ $assessment->home_ownership_status ?? 'Milik Sendiri' }}</td>
            <td class="label-col">Kondisi Dinding Rumah</td>
            <td class="val-col">{{ $assessment->wall_type ?? 'Tembok Permanen' }}</td>
        </tr>
        <tr>
            <td class="label-col">Kondisi Lantai</td>
            <td class="val-col">{{ $assessment->floor_type ?? 'Keramik' }}</td>
            <td class="label-col">Kondisi Atap</td>
            <td class="val-col">{{ $assessment->roof_type ?? 'Genteng' }}</td>
        </tr>
        <tr>
            <td class="label-col">Sumber Air Bersih</td>
            <td class="val-col">{{ $assessment->water_source ?? 'Sumur Bor' }}</td>
            <td class="label-col">Daya Listrik PLN</td>
            <td class="val-col">{{ $assessment->electricity_power ?? 'PLN 1300 VA' }}</td>
        </tr>
    </table>

    {{-- D. ASET RUMAH TANGGA --}}
    <div class="section-header">D. ASET RUMAH TANGGA</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Kepemilikan Tanah / Rumah</td>
            <td class="full-val" colspan="3">{{ $assessment->land_home_assets ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Kendaraan Bermotor</td>
            <td class="full-val" colspan="3">{{ $assessment->vehicle_assets ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Peralatan Elektronik</td>
            <td class="full-val" colspan="3">{{ $assessment->electronic_assets ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Simpanan / Emas / Tabungan</td>
            <td class="full-val" colspan="3">{{ $assessment->savings_gold_assets ?? '-' }}</td>
        </tr>
    </table>

    {{-- E. KELAYAKAN SOSIAL & KARAKTER --}}
    <div class="section-header">E. KELAYAKAN SOSIAL & KARAKTER</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Hubungan dengan Lingkungan</td>
            <td class="val-col">{{ $assessment->community_relation ?? 'Sangat Baik' }}</td>
            <td class="label-col">Keaktifan di Rembug Pusat (RP)</td>
            <td class="val-col">{{ $assessment->rembug_pusat_activity ?? 'Aktif & Disiplin' }}</td>
        </tr>
        <tr>
            <td class="label-col">Reputasi, Kejujuran & Disiplin</td>
            <td class="full-val" colspan="3">{{ $assessment->reputation_character ?? 'Amanah, bertutur kata sopan, beritikad baik dalam bermuamalah.' }}</td>
        </tr>
    </table>

    {{-- F. KESIMPULAN UJI KELAYAKAN --}}
    <div class="section-header">F. KESIMPULAN UJI KELAYAKAN</div>
    <table class="data-table">
        <tr>
            <td class="label-col" style="font-size: 9pt;">Hasil Rekomendasi Kelayakan</td>
            <td class="full-val" colspan="3">
                @if ($assessment->result === 'memenuhi_syarat')
                    <span class="badge badge-layak">MEMENUHI SYARAT (LAYAK)</span>
                    <span style="margin-left: 8px; color: #065f46; font-size: 8pt;">Calon anggota memenuhi seluruh kriteria kelayakan sosial dan rumah tangga Kopsyah BMI.</span>
                @elseif ($assessment->result === 'perlu_pertimbangan')
                    <span class="badge badge-bersyarat">PERLU PERTIMBANGAN</span>
                    <span style="margin-left: 8px; color: #92400e; font-size: 8pt;">Memerlukan pendampingan khusus atau persetujuan pimpinan cabang.</span>
                @else
                    <span class="badge badge-tidak">TIDAK MEMENUHI SYARAT</span>
                    <span style="margin-left: 8px; color: #991b1b; font-size: 8pt;">Tidak memenuhi kriteria uji kelayakan awal keanggotaan.</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label-col">Catatan Verifikator Lapangan</td>
            <td class="full-val" colspan="3">{{ $assessment->notes ?? 'Verifikasi data fisik rumah dan wawancara pasangan telah dilakukan dengan hasil memuaskan.' }}</td>
        </tr>
        @if ($assessment->validator_notes)
        <tr>
            <td class="label-col">Catatan Validasi Pimpinan</td>
            <td class="full-val" colspan="3" style="color: #007d3e;">{{ $assessment->validator_notes }}</td>
        </tr>
        @endif
    </table>

    {{-- PENGESAHAN --}}
    <table class="signatures-table">
        <tr>
            <td>
                <div class="sig-role">Petugas Surveyor / Verifikator</div>
                <div class="sig-name">{{ $assessment->surveyor?->name ?? 'Petugas Lapangan' }}</div>
                <div style="font-size: 7.5pt; color: #666;">Tanggal: {{ $assessment->assessment_date?->format('d/m/Y') }}</div>
            </td>
            <td>
                <div class="sig-role">Diperiksa oleh,<br>Asisten Manajer</div>
                <div class="sig-name">{{ $assessment->validator?->name ?? 'Asisten Manajer' }}</div>
                <div style="font-size: 7.5pt; color: #666;">Status: {{ ucfirst($assessment->status) }}</div>
            </td>
            <td>
                <div class="sig-role">Disetujui oleh,<br>Manajer Cabang</div>
                <div class="sig-name">{{ $assessment->validator?->name ?? 'Manajer Cabang' }}</div>
                <div style="font-size: 7.5pt; color: #666;">Tanggal: {{ $assessment->validated_at?->format('d/m/Y') ?? date('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} &bull; Sistem Informasi Evaluasi & Pembinaan Usaha Koperasi Syariah BMI &bull; Dokumen Uji Kelayakan
    </div>

</body>
</html>
