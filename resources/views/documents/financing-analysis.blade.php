<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Analisis Pembiayaan - {{ $analysis->analysis_number }}</title>
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
            width: 32%;
            background-color: #fafafa;
            font-weight: 600;
            color: #333;
        }
        .val-col {
            width: 68%;
        }
        .number-col {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 600;
        }
        .highlight-row {
            background-color: #f5f9f5;
            font-weight: bold;
        }
        .total-row {
            background-color: #e8f3e8;
            font-weight: bold;
            border-top: 2px solid #009a4c;
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
        <a href="{{ route('documents.financing-analysis.pdf', $analysis->id) }}" class="btn-print" style="background:#007d3e; margin-left:6px;">Download PDF</a>
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
                <div class="header-subtitle">FORMULIR ANALISIS PEMBIAYAAN</div>
                <div class="header-meta">Kantor Operasional &bull; Melayani dengan Hati Nurani Sesuai Prinsip Syariah</div>
            </td>
        </tr>
    </table>

    {{-- METADATA BAR --}}
    <table class="meta-bar">
        <tr>
            <td style="width: 25%;"><strong>No. Formulir:</strong> {{ $analysis->analysis_number }}</td>
            <td style="width: 25%;"><strong>Kantor Cabang:</strong> {{ $analysis->branch?->name ?? 'Cabang Pusat' }}</td>
            <td style="width: 25%;"><strong>Desa / RP:</strong> {{ $analysis->rembug_pusat ?? $analysis->member?->rembug_pusat ?? '-' }}</td>
            <td style="width: 25%; text-align: right;"><strong>Tanggal:</strong> {{ $analysis->analysis_date?->format('d/m/Y') ?? date('d/m/Y') }}</td>
        </tr>
    </table>

    {{-- A. DATA ANGGOTA --}}
    <div class="section-header">A. DATA ANGGOTA</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Nama Anggota</td>
            <td class="val-col"><strong>{{ $analysis->member?->full_name }}</strong></td>
            <td class="label-col">Nomor Anggota</td>
            <td class="val-col"><strong>{{ $analysis->member?->member_number }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">Nama Suami / Istri</td>
            <td class="val-col">{{ $analysis->spouse_name ?? $analysis->member?->spouse_name ?? '-' }}</td>
            <td class="label-col">Rembug Pusat (RP)</td>
            <td class="val-col">{{ $analysis->rembug_pusat ?? $analysis->member?->rembug_pusat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat Domisili</td>
            <td class="val-col">{{ $analysis->member?->address ?? '-' }}</td>
            <td class="label-col">Lama Keanggotaan / Thn Registrasi</td>
            <td class="val-col">{{ $analysis->registration_year ?? $analysis->member?->registration_year ?? '-' }}</td>
        </tr>
    </table>

    {{-- B. PEMBIAYAAN --}}
    <div class="section-header">B. PEMBIAYAAN</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Rencana Pengajuan</td>
            <td class="val-col number-col">Rp {{ number_format($analysis->proposed_amount, 0, ',', '.') }}</td>
            <td class="label-col">Pembiayaan Terakhir (BMI)</td>
            <td class="val-col number-col">Rp {{ number_format($analysis->last_financing_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label-col">Plafon Pembiayaan Direkomendasikan</td>
            <td class="val-col number-col" style="color: #009a4c;">
                <strong>Rp {{ number_format($analysis->approved_ceiling ?? $analysis->proposed_amount, 0, ',', '.') }}</strong>
            </td>
            <td class="label-col">Plafond Pembiayaan Investasi</td>
            <td class="val-col number-col">Rp {{ number_format($analysis->investment_ceiling, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label-col">Tujuan Penggunaan</td>
            <td class="val-col" colspan="3">{{ $analysis->financing_purpose ?? 'Modal Kerja Usaha' }}</td>
        </tr>
        <tr>
            <td class="label-col">Pembiayaan di Tempat Lain</td>
            <td class="val-col number-col" colspan="3">
                Rp {{ number_format($analysis->financing_other_institution, 0, ',', '.') }}
                @if ($analysis->financing_other_institution == 0)
                    <span style="font-weight: normal; color: #555;">(Tidak ada pinjaman lembaga lain)</span>
                @endif
            </td>
        </tr>
    </table>

    {{-- C. KAPASITAS USAHA --}}
    <div class="section-header">C. KAPASITAS USAHA</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Nama Usaha</td>
            <td class="val-col"><strong>{{ $analysis->business?->name ?? 'Usaha Anggota' }}</strong></td>
            <td class="label-col">Jenis Usaha</td>
            <td class="val-col">{{ $analysis->business_type ?? $analysis->business?->business_type ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Tahun Mulai Usaha / Lama Usaha</td>
            <td class="val-col">
                {{ $analysis->business_start_year ?? $analysis->business?->start_year ?? '-' }}
                @if ($analysis->business?->business_age_months)
                    ({{ $analysis->business->business_age_months }} bulan)
                @endif
            </td>
            <td class="label-col">Tenaga Kerja</td>
            <td class="val-col">{{ $analysis->workforce_count ?? $analysis->business?->workforce_count ?? 0 }} Orang</td>
        </tr>
        <tr>
            <td class="label-col">Alamat Tempat Usaha</td>
            <td class="val-col" colspan="3">{{ $analysis->business?->address ?? $analysis->member?->address }}</td>
        </tr>
        <tr>
            <td class="label-col">Omset Rata-rata Harian</td>
            <td class="val-col number-col">Rp {{ number_format($analysis->daily_turnover ?: ($analysis->monthly_turnover / 30), 0, ',', '.') }}</td>
            <td class="label-col">Omset Rata-rata Bulanan</td>
            <td class="val-col number-col">Rp {{ number_format($analysis->monthly_turnover, 0, ',', '.') }}</td>
        </tr>
        <tr class="highlight-row">
            <td class="label-col">Pendapatan Bersih Usaha (Per Bulan)</td>
            <td class="val-col number-col" colspan="3" style="color: #007d3e;">
                Rp {{ number_format($analysis->net_business_income ?: $analysis->business_income, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    {{-- D. ASET DAN SIMPANAN --}}
    <div class="section-header">D. ASET DAN SIMPANAN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Aset Usaha & Properti</th>
                <th style="width: 25%;">Simpanan Koperasi</th>
                <th style="width: 25%;">Elektronik</th>
                <th style="width: 25%;">Kendaraan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="number-col">Rp {{ number_format($analysis->business_assets_estimate, 0, ',', '.') }}</td>
                <td class="number-col">Rp {{ number_format($analysis->savings_amount, 0, ',', '.') }}</td>
                <td class="number-col">Rp {{ number_format($analysis->electronic_assets_estimate, 0, ',', '.') }}</td>
                <td class="number-col">Rp {{ number_format($analysis->vehicle_assets_estimate, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2"><strong>Total Estimasi Nilai Aset & Simpanan</strong></td>
                <td colspan="2" class="number-col" style="font-size: 9.5pt; color: #007d3e;">
                    <strong>Rp {{ number_format($analysis->total_assets_estimate, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- E. KEUANGAN (ARUS KAS BULANAN) --}}
    <div class="section-header">E. KEUANGAN (ARUS KAS RUMAH TANGGA & USAHA)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th colspan="2" style="width: 50%; background-color: #e6f4ea; color: #065f46;">PENDAPATAN / ARUS MASUK</th>
                <th colspan="2" style="width: 50%; background-color: #fef2f2; color: #991b1b;">PENGELUARAN / ARUS KELUAR</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label-col">Pendapatan Usaha</td>
                <td class="number-col">Rp {{ number_format($analysis->business_income, 0, ',', '.') }}</td>
                <td class="label-col">Pengeluaran Rumah Tangga</td>
                <td class="number-col">Rp {{ number_format($analysis->household_expenses, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label-col">Pendapatan Suami / Istri</td>
                <td class="number-col">Rp {{ number_format($analysis->spouse_income, 0, ',', '.') }}</td>
                <td class="label-col">Biaya Operasional Usaha</td>
                <td class="number-col">Rp {{ number_format($analysis->business_expenses, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label-col">Pendapatan Lain-lain</td>
                <td class="number-col">Rp 0</td>
                <td class="label-col">Angsuran di Tempat Lain</td>
                <td class="number-col">Rp {{ number_format($analysis->other_installments, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>Total Pendapatan (A)</strong></td>
                <td class="number-col" style="color: #065f46;">
                    <strong>Rp {{ number_format($analysis->total_income, 0, ',', '.') }}</strong>
                </td>
                <td><strong>Total Biaya (B)</strong></td>
                <td class="number-col" style="color: #991b1b;">
                    <strong>Rp {{ number_format($analysis->total_expenses, 0, ',', '.') }}</strong>
                </td>
            </tr>
            <tr class="highlight-row">
                <td class="label-col">Pendapatan Perkapita</td>
                <td class="number-col" colspan="3">
                    Rp {{ number_format($analysis->per_capita_income, 0, ',', '.') }} / Jiwa
                    <span style="font-weight: normal; color: #666;">(Berdasarkan tanggungan keluarga)</span>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- F. KEMAMPUAN ANGGOTA (CAPACITY TO REPAY) --}}
    <div class="section-header">F. KEMAMPUAN ANGGOTA (CAPACITY TO REPAY)</div>
    <table class="data-table">
        <tr class="highlight-row">
            <td class="label-col" style="font-size: 9pt;">Saving Capacity (Sisa Bersih Kas Bulanan)</td>
            <td class="val-col number-col" style="font-size: 9.5pt; color: #007d3e;">
                <strong>Rp {{ number_format($analysis->saving_capacity, 0, ',', '.') }}</strong>
                <span style="font-size: 7.5pt; color: #555; font-weight: normal;">(Total Pendapatan - Total Biaya)</span>
            </td>
        </tr>
        <tr class="total-row">
            <td class="label-col" style="font-size: 9pt;">Kemampuan Mengangsur (Maksimal Angsuran)</td>
            <td class="val-col number-col" style="font-size: 10pt; color: #009a4c;">
                <strong>Rp {{ number_format($analysis->installment_capacity, 0, ',', '.') }}</strong>
                <span style="font-size: 7.5pt; color: #555; font-weight: normal;">(75% dari Saving Capacity)</span>
            </td>
        </tr>
        <tr>
            <td class="label-col">Kesimpulan Analisis</td>
            <td class="val-col">
                @if ($analysis->conclusion === 'layak')
                    <span class="badge badge-layak">LAYAK</span>
                    <span style="margin-left: 8px; color: #065f46; font-size: 8pt;">Kapasitas keuangan dan usaha sangat memadai untuk pengajuan pembiayaan.</span>
                @elseif ($analysis->conclusion === 'layak_bersyarat')
                    <span class="badge badge-bersyarat">LAYAK BERSYARAT</span>
                    <span style="margin-left: 8px; color: #92400e; font-size: 8pt;">Dapat diproses dengan penyesuaian plafon atau mitigasi agunan/pendampingan.</span>
                @else
                    <span class="badge badge-tidak">TIDAK LAYAK</span>
                    <span style="margin-left: 8px; color: #991b1b; font-size: 8pt;">Arus kas tidak mencukupi untuk menanggung beban angsuran tambahan.</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label-col">Catatan Analis & Pertimbangan</td>
            <td class="val-col">{{ $analysis->notes ?? 'Usaha telah berjalan dengan baik, arus perputaran kas stabil dan hubungan sosial di rembug pusat sangat baik.' }}</td>
        </tr>
        @if ($analysis->validator_notes)
        <tr>
            <td class="label-col">Catatan Persetujuan Manajer</td>
            <td class="val-col" style="color: #007d3e;">{{ $analysis->validator_notes }}</td>
        </tr>
        @endif
    </table>

    {{-- LEMBAR TANDA TANGAN & PENGESAHAN --}}
    <table class="signatures-table">
        <tr>
            <td>
                <div class="sig-role">Dibuat oleh,<br>Petugas Analis / Lapangan</div>
                <div class="sig-name">{{ $analysis->analyst?->name ?? 'Petugas Lapangan' }}</div>
                <div style="font-size: 7.5pt; color: #666;">Tanggal: {{ $analysis->analysis_date?->format('d/m/Y') }}</div>
            </td>
            <td>
                <div class="sig-role">Diperiksa oleh,<br>Asisten Manajer</div>
                <div class="sig-name">{{ $analysis->validator?->name ?? 'Asisten Manajer Cabang' }}</div>
                <div style="font-size: 7.5pt; color: #666;">Status: {{ ucfirst($analysis->status) }}</div>
            </td>
            <td>
                <div class="sig-role">Disetujui oleh,<br>Manajer Cabang</div>
                <div class="sig-name">{{ $analysis->validator?->name ?? 'Manajer Cabang' }}</div>
                <div style="font-size: 7.5pt; color: #666;">Tanggal: {{ $analysis->validated_at?->format('d/m/Y') ?? date('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} &bull; Sistem Informasi Evaluasi & Pembinaan Usaha Koperasi Syariah BMI &bull; Dokumen Rahasia & Operasional
    </div>

</body>
</html>
