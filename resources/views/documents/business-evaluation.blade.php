<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Evaluasi Usaha - {{ $docNumber ?? ('EV-' . $evaluation->id) }}</title>
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
        .photo-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .photo-grid td {
            border: 1px solid #e0e0e0;
            padding: 6px;
            text-align: center;
            vertical-align: top;
            width: 50%;
        }
        .photo-img {
            max-width: 100%;
            max-height: 140px;
            object-fit: cover;
            border: 1px solid #ccc;
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
        <a href="{{ route('documents.business-evaluation.pdf', $evaluation->id) }}" class="btn-print" style="background:#007d3e; margin-left:6px;">Download PDF</a>
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
                <div class="header-subtitle">LEMBAR HASIL EVALUASI & PEMBINAAN USAHA ANGGOTA</div>
                <div class="header-meta">Monitoring Kinerja Usaha, Perkembangan Omzet, dan Pembinaan Berkelanjutan</div>
            </td>
        </tr>
    </table>

    {{-- METADATA BAR --}}
    <table class="meta-bar">
        <tr>
            <td style="width: 25%;"><strong>No. Evaluasi:</strong> {{ $docNumber ?? ('EV-' . $evaluation->id) }}</td>
            <td style="width: 25%;"><strong>Periode:</strong> {{ $evaluation->visit?->evaluation_period ?? date('Y-m') }}</td>
            <td style="width: 25%;"><strong>Tgl Kunjungan:</strong> {{ $evaluation->visit?->visit_date?->format('d/m/Y') ?? date('d/m/Y') }}</td>
            <td style="width: 25%; text-align: right;"><strong>Status:</strong> <span class="badge badge-layak">{{ strtoupper($evaluation->status?->value ?? $evaluation->status) }}</span></td>
        </tr>
    </table>

    {{-- A. IDENTITAS ANGGOTA & USAHA --}}
    <div class="section-header">A. IDENTITAS ANGGOTA & USAHA BINAAN</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Nama Anggota</td>
            <td class="val-col"><strong>{{ $evaluation->business?->member?->full_name }}</strong></td>
            <td class="label-col">Nomor Anggota</td>
            <td class="val-col"><strong>{{ $evaluation->business?->member?->member_number }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">Nama Usaha</td>
            <td class="val-col"><strong>{{ $evaluation->business?->name }}</strong></td>
            <td class="label-col">Jenis / Sektor Usaha</td>
            <td class="val-col">{{ $evaluation->business?->business_type }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat Tempat Usaha</td>
            <td class="val-col">{{ $evaluation->business?->address ?? $evaluation->business?->member?->address }}</td>
            <td class="label-col">Rembug Pusat / Cabang</td>
            <td class="val-col">{{ $evaluation->business?->member?->rembug_pusat ?? 'Rembug Pusat' }} &bull; {{ $evaluation->business?->branch?->name ?? 'Cabang Pusat' }}</td>
        </tr>
        <tr>
            <td class="label-col">Lama Usaha Berjalan</td>
            <td class="val-col">{{ $evaluation->business?->business_age_months ?? '-' }} Bulan</td>
            <td class="label-col">Kontak Telepon / WhatsApp</td>
            <td class="val-col">{{ $evaluation->business?->member?->phone ?? '-' }}</td>
        </tr>
    </table>

    {{-- B. OBSERVASI TEMUAN LAPANGAN TERSTRUKTUR --}}
    <div class="section-header">B. OBSERVASI TEMUAN LAPANGAN TERSTRUKTUR</div>
    <table class="data-table">
        <tr>
            <td class="label-col">1. Kondisi Usaha</td>
            <td class="full-val" colspan="3">{{ $evaluation->visit?->business_condition ?? 'Tempat usaha beroperasi aktif, fasilitas dan penataan barang tertata rapi.' }}</td>
        </tr>
        <tr>
            <td class="label-col">2. Perkembangan Omzet</td>
            <td class="full-val" colspan="3">{{ $evaluation->visit?->revenue_trend ?? 'Penjualan stabil dengan perputaran kas harian lancar.' }}</td>
        </tr>
        <tr>
            <td class="label-col">3. Aktivitas Usaha</td>
            <td class="full-val" colspan="3">{{ $evaluation->visit?->business_activity ?? 'Jam buka konsisten, pelayanan pelanggan aktif setiap hari.' }}</td>
        </tr>
        <tr>
            <td class="label-col">4. Kendala Usaha</td>
            <td class="full-val" colspan="3">{{ $evaluation->visit?->business_obstacles ?? 'Tidak ada kendala fatal yang mengancam operasional usaha.' }}</td>
        </tr>
        <tr>
            <td class="label-col">5. Temuan Khusus Lapangan</td>
            <td class="full-val" colspan="3">{{ $evaluation->visit?->field_findings ?? $evaluation->visit?->field_notes ?? '-' }}</td>
        </tr>
    </table>

    {{-- C. SKORING 5 PARAMETER EVALUASI TERSTANDARISASI --}}
    <div class="section-header">C. SKORING 5 PARAMETER EVALUASI TERSTANDARISASI (BOBOT 100%)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 32%;">Parameter Evaluasi</th>
                <th style="width: 15%; text-align: center;">Bobot (%)</th>
                <th style="width: 15%; text-align: center;">Skor (0-100)</th>
                <th style="width: 18%; text-align: right;">Skor Tertimbang</th>
                <th style="width: 15%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $details = $evaluation->details->keyBy(fn($d) => $d->parameter?->code ?? $d->parameter_id);
                $params = [
                    ['code' => 'kondisi_usaha', 'name' => '1. Kondisi Usaha', 'weight' => 20],
                    ['code' => 'perkembangan_omzet', 'name' => '2. Perkembangan Omzet', 'weight' => 25],
                    ['code' => 'aktivitas_usaha', 'name' => '3. Aktivitas Usaha', 'weight' => 20],
                    ['code' => 'pengelolaan_keuangan', 'name' => '4. Pengelolaan Keuangan', 'weight' => 20],
                    ['code' => 'kendala_usaha', 'name' => '5. Kendala Usaha', 'weight' => 15],
                ];
            @endphp
            @foreach ($params as $idx => $p)
                @php
                    $detail = $details->get($p['code']);
                    $score = $detail?->score ?? 80;
                    $weighted = ($score * $p['weight']) / 100;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td><strong>{{ $p['name'] }}</strong></td>
                    <td style="text-align: center;">{{ $p['weight'] }}%</td>
                    <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ number_format($score, 1) }}</td>
                    <td style="text-align: right; font-family: monospace; font-weight: bold; color: #007d3e;">{{ number_format($weighted, 2) }}</td>
                    <td style="text-align: center; font-size: 7.5pt;">
                        @if ($score >= 80)
                            <span style="color: #065f46; font-weight: bold;">Baik</span>
                        @elseif ($score >= 60)
                            <span style="color: #92400e; font-weight: bold;">Cukup</span>
                        @else
                            <span style="color: #991b1b; font-weight: bold;">Perlu Perhatian</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr style="background-color: #e8f3e8; border-top: 2px solid #009a4c; font-weight: bold; font-size: 9.5pt;">
                <td colspan="2" style="text-align: right;"><strong>TOTAL SKOR AKHIR:</strong></td>
                <td style="text-align: center;"><strong>100%</strong></td>
                <td colspan="2" style="text-align: right; color: #007d3e; font-family: monospace; font-size: 11pt;">
                    <strong>{{ number_format($evaluation->total_score, 2) }} / 100</strong>
                </td>
                <td style="text-align: center;">
                    @if ($evaluation->total_score >= 80)
                        <span class="badge badge-layak">DIREKOMENDASIKAN</span>
                    @elseif ($evaluation->total_score >= 60)
                        <span class="badge badge-bersyarat">PEMBINAAN</span>
                    @else
                        <span class="badge badge-tidak">TIDAK REKOMENDASI</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <table class="data-table" style="margin-top: 4px;">
        <tr>
            <td class="label-col" style="width: 25%;">Kesimpulan Rekomendasi</td>
            <td class="full-val">
                @php
                    $rec = $evaluation->recommendation?->value ?? $evaluation->recommendation;
                @endphp
                @if ($rec === 'recommended')
                    <strong style="color: #065f46;">DIREKOMENDASIKAN</strong> &bull; Usaha berkembang sangat baik, mandiri dan memenuhi standar kelayakan fasilitas lanjutan BMI.
                @elseif ($rec === 'continued_coaching')
                    <strong style="color: #92400e;">PEMBINAAN LANJUTAN</strong> &bull; Usaha beroperasi normal namun memerlukan pendampingan pengelolaan kas dan inovasi penjualan.
                @else
                    <strong style="color: #991b1b;">TIDAK DIREKOMENDASIKAN</strong> &bull; Terdeteksi penurunan signifikan / masalah berat yang membutuhkan penanganan khusus.
                @endif
            </td>
        </tr>
        @if ($evaluation->recommendation_reason)
        <tr>
            <td class="label-col">Alasan & Justifikasi</td>
            <td class="full-val">{{ $evaluation->recommendation_reason }}</td>
        </tr>
        @endif
    </table>

    {{-- D. DOKUMENTASI FOTO LAPANGAN --}}
    @if ($evaluation->visit?->documents && $evaluation->visit->documents->count() > 0)
    <div class="section-header">D. DOKUMENTASI FOTO LAPANGAN</div>
    <table class="photo-grid">
        <tr>
            @foreach ($evaluation->visit->documents->take(2) as $doc)
                <td>
                    <img src="{{ public_path('storage/' . $doc->file_path) }}"
                         alt="Foto Lapangan"
                         class="photo-img"
                         onerror="this.style.display='none'">
                    <div style="font-size: 7.5pt; color: #444; margin-top: 3px;">
                        {{ $doc->caption ?? 'Dokumentasi Tempat Usaha' }} &bull; {{ $doc->created_at?->format('d/m/Y H:i') }}
                    </div>
                </td>
            @endforeach
        </tr>
    </table>
    @endif

    {{-- E. TINDAK LANJUT PEMBINAAN & TARGET PERBAIKAN --}}
    <div class="section-header">E. TINDAK LANJUT PEMBINAAN & TARGET PERBAIKAN</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Rencana Tindak Lanjut Petugas</td>
            <td class="full-val" colspan="3">{{ $evaluation->visit?->action_plan ?? 'Melakukan pendampingan pencatatan pembukuan sederhana dan pemantauan stok berkala.' }}</td>
        </tr>
        <tr>
            <td class="label-col">Target Perbaikan Anggota</td>
            <td class="full-val" colspan="3">{{ $evaluation->visit?->improvement_target ?? 'Menerapkan pemisahan kas usaha dari dompet belanja keluarga dan peningkatan display barang.' }}</td>
        </tr>
        @if ($evaluation->coachingRecommendations && $evaluation->coachingRecommendations->count() > 0)
        <tr>
            <td class="label-col">Program Pembinaan Diusulkan</td>
            <td class="full-val" colspan="3">
                @foreach ($evaluation->coachingRecommendations as $coach)
                    <div>&bull; <strong>{{ $coach->title }}</strong>: {{ $coach->description }} (Target: {{ $coach->target_date?->format('d/m/Y') ?? '-' }})</div>
                @endforeach
            </td>
        </tr>
        @endif
    </table>

    {{-- F. LEMBAR PENGESAHAN & VALIDASI --}}
    <table class="signatures-table">
        <tr>
            <td>
                <div class="sig-role">Petugas Lapangan / Pendamping</div>
                <div class="sig-name">{{ $evaluation->submittedBy?->name ?? 'Petugas Lapangan' }}</div>
                <div style="font-size: 7.5pt; color: #666;">Tanggal: {{ $evaluation->submitted_at?->format('d/m/Y') ?? date('d/m/Y') }}</div>
            </td>
            <td>
                <div class="sig-role">Diperiksa oleh,<br>Asisten Manajer</div>
                <div class="sig-name">{{ $evaluation->validatedBy?->name ?? 'Asisten Manajer Cabang' }}</div>
                <div style="font-size: 7.5pt; color: #666;">Status: {{ ucfirst($evaluation->status?->value ?? $evaluation->status) }}</div>
            </td>
            <td>
                <div class="sig-role">Disetujui oleh,<br>Manajer Cabang</div>
                <div class="sig-name">{{ $evaluation->validatedBy?->name ?? 'Manajer Cabang' }}</div>
                <div style="font-size: 7.5pt; color: #666;">Tanggal: {{ $evaluation->validated_at?->format('d/m/Y') ?? date('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} &bull; Sistem Informasi Evaluasi & Pembinaan Usaha Koperasi Syariah BMI &bull; Lembar Evaluasi Resmi
    </div>

</body>
</html>
