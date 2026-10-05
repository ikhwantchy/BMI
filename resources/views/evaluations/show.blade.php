@extends('layouts.app')

@section('title', 'Hasil Evaluasi: ' . $evaluation->business->name)
@section('page-title', 'Hasil Evaluasi Usaha')

@section('content')
<div class="py-4 space-y-6">

    {{-- Breadcrumb & Top Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 pb-4">
        <div class="flex items-center space-x-2 text-xs text-gray-500">
            <a href="{{ route('evaluations.index') }}" class="hover:text-emerald-700 transition">Evaluasi</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">{{ $evaluation->business->name }}</span>
            <span>/</span>
            <span class="text-gray-500 font-mono">{{ $evaluation->visit?->evaluation_period ?? '-' }}</span>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="btn-secondary">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Lembar
            </button>
            <a href="{{ route('visits.show', $evaluation->visit) }}" class="btn-secondary">
                Ke Kunjungan
            </a>
        </div>
    </div>

    {{-- Result Score & Recommendation Hero Card --}}
    <div class="bg-white border border-gray-200">
        <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-gray-200">
            <div>
                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Hasil Evaluasi Pembinaan Usaha</span>
                <h2 class="text-lg font-semibold text-gray-900 mt-1">{{ $evaluation->business->name }}</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Anggota: <span class="font-medium text-gray-800">{{ $evaluation->business->member->full_name }}</span> ({{ $evaluation->business->member->member_number }})
                    &bull; Kunjungan: {{ $evaluation->visit?->visit_date->translatedFormat('d F Y') }} (Periode: {{ $evaluation->visit?->evaluation_period }})
                </p>
            </div>

            {{-- Score Box --}}
            <div class="flex items-center space-x-6 bg-gray-50 p-4 border border-gray-200">
                <div class="text-center">
                    <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider block">Skor Akhir</span>
                    <div class="flex items-baseline justify-center space-x-1 mt-0.5 font-mono">
                        <span class="text-3xl font-semibold text-gray-900">
                            {{ number_format($evaluation->final_score, 1) }}
                        </span>
                        <span class="text-xs text-gray-400">/ 100</span>
                    </div>
                </div>

                <div class="h-10 w-px bg-gray-200"></div>

                <div>
                    <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider block">Rekomendasi Sistem</span>
                    @if($evaluation->recommendation)
                        <div class="mt-1">
                            <span class="badge border
                                {{ $evaluation->recommendation->value === 'lanjutkan_tingkatkan' ? 'bg-emerald-50 border-emerald-300 text-emerald-900' :
                                   ($evaluation->recommendation->value === 'pembinaan_khusus' ? 'bg-amber-50 border-amber-300 text-amber-900' : 'bg-red-50 border-red-300 text-red-900') }}">
                                {{ $evaluation->recommendation->label() }}
                            </span>
                        </div>
                    @else
                        <span class="text-xs text-gray-400">Belum dihitung</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Workflow & Approval Status Banner --}}
        <div class="px-6 py-3 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs border-b border-gray-100">
            <div class="flex items-center space-x-2">
                <span class="font-semibold text-gray-600">Status Validasi:</span>
                <span class="badge border bg-{{ $evaluation->status->badgeColor() }}-50 border-{{ $evaluation->status->badgeColor() }}-200 text-{{ $evaluation->status->badgeColor() }}-800">
                    {{ $evaluation->status->label() }}
                </span>
                @if($evaluation->submittedBy)
                    <span class="text-gray-400">&bull; Diajukan: {{ $evaluation->submittedBy->name }} ({{ $evaluation->submitted_at?->format('d/m/Y H:i') }})</span>
                @endif
                @if($evaluation->validatedBy)
                    <span class="text-gray-400">&bull; Divalidasi: {{ $evaluation->validatedBy->name }} ({{ $evaluation->validated_at?->format('d/m/Y H:i') }})</span>
                @endif
            </div>

            {{-- Action Forms based on Status --}}
            <div class="flex items-center space-x-2">
                @if($evaluation->isEditable())
                    @can('update', $evaluation)
                    <form action="{{ route('evaluations.calculate', $evaluation) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-secondary py-1 text-xs">
                            Hitung Ulang
                        </button>
                    </form>
                    <form action="{{ route('evaluations.submit', $evaluation) }}" method="POST" onsubmit="return confirm('Ajukan evaluasi ini ke Manajer untuk divalidasi?')">
                        @csrf
                        <button type="submit" class="btn-primary py-1 text-xs">
                            @if($evaluation->isNeedsRevision()) Ajukan Ulang Setelah Revisi @else Ajukan Validasi @endif
                        </button>
                    </form>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    {{-- Validation Action Box for Managers if Waiting Validation --}}
    @can('approve', $evaluation)
        <div class="bg-amber-50 border border-amber-200 p-6 space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="font-semibold text-amber-950 text-sm uppercase tracking-wider">Persetujuan & Validasi Manajer</h3>
                    <p class="text-xs text-amber-800 mt-0.5">Sebagai Manajer, periksa data skor dan rekomendasi di bawah sebelum melakukan persetujuan.</p>
                </div>
                <span class="badge border border-amber-300 bg-amber-100 text-amber-900">Aksi Manajer</span>
            </div>

            <div x-data="{ validatorNotes: '' }">
                <div class="mb-3">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-amber-900 mb-1">
                        Catatan / Arahan Manajer (Wajib diisi jika menolak)
                    </label>
                    <textarea x-model="validatorNotes"
                              rows="2"
                              placeholder="Tuliskan catatan persetujuan, instruksi pembinaan tambahan, atau alasan penolakan..."
                              class="input-base border-amber-300 bg-white focus:border-amber-600"></textarea>
                </div>

                <div class="flex items-center space-x-3 justify-end">
                    <form action="{{ route('evaluations.reject', $evaluation) }}" method="POST"
                          onsubmit="if(!validatorNotes.trim()){ alert('Mohon isi catatan alasan penolakan terlebih dahulu.'); return false; } return confirm('Tolak evaluasi ini dan kembalikan ke petugas?')">
                        @csrf
                        <input type="hidden" name="validator_notes" :value="validatorNotes">
                        <button type="submit" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white text-xs font-semibold transition">
                            Tolak Evaluasi
                        </button>
                    </form>

                    @can('revise', $evaluation)
                    <form action="{{ route('evaluations.revise', $evaluation) }}" method="POST"
                          onsubmit="if(!validatorNotes.trim()){ alert('Mohon isi catatan revisi terlebih dahulu.'); return false; } return confirm('Kembalikan evaluasi ini ke petugas untuk direvisi?')">
                        @csrf
                        <input type="hidden" name="validator_notes" :value="validatorNotes">
                        <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold transition">
                            Minta Revisi
                        </button>
                    </form>
                    @endcan

                    <form action="{{ route('evaluations.validate', $evaluation) }}" method="POST"
                          onsubmit="return confirm('Validasi dan setujui evaluasi ini?')">
                        @csrf
                        <input type="hidden" name="validator_notes" :value="validatorNotes">
                        <button type="submit" class="btn-primary">
                            Setujui & Validasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- Validator Notes Display --}}
    @if($evaluation->validator_notes)
        @php
            $noteColor = match($evaluation->status) {
                \App\Enums\EvaluationStatus::Validated    => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800', 'body' => 'text-emerald-950'],
                \App\Enums\EvaluationStatus::NeedsRevision => ['bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'text' => 'text-orange-800', 'body' => 'text-orange-950'],
                default                                   => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-800', 'body' => 'text-red-950'],
            };
        @endphp
        <div class="p-4 border {{ $noteColor['bg'] }} {{ $noteColor['border'] }}">
            <span class="text-xs font-semibold uppercase tracking-wider {{ $noteColor['text'] }}">
                {{ $evaluation->status === \App\Enums\EvaluationStatus::NeedsRevision ? 'Catatan Revisi' : 'Catatan Manajer' }}
                ({{ $evaluation->validatedBy?->name }}):
            </span>
            <p class="text-xs mt-1 {{ $noteColor['body'] }} leading-relaxed">
                {{ $evaluation->validator_notes }}
            </p>
        </div>
    @endif

    {{-- Parameter Breakdown Table --}}
    <div class="bg-white border border-gray-200">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
            <div>
                <h3 class="font-semibold text-gray-900 text-xs uppercase tracking-wider">Rincian Skor Per Parameter</h3>
                <p class="text-[11px] text-gray-500">Perhitungan skor terbobot sesuai formula Koperasi Syariah BMI</p>
            </div>
            <span class="text-[11px] text-gray-400 font-mono">Formula: Skor × Bobot%</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left data-table">
                <thead>
                    <tr>
                        <th>Parameter Penilaian</th>
                        <th class="text-center">Nilai Lapangan (0-100)</th>
                        <th class="text-center">Bobot</th>
                        <th class="text-center">Skor Terbobot</th>
                        <th>Catatan Temuan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalWeight = 0; $totalWeighted = 0; @endphp
                    @foreach($evaluation->details as $detail)
                        @php
                            $weight = $detail->parameter?->weight_percent ?? 0;
                            $weighted = ($detail->score * $weight) / 100;
                            $totalWeight += $weight;
                            $totalWeighted += $weighted;
                        @endphp
                        <tr>
                            <td>
                                <span class="font-medium text-gray-900 block">{{ $detail->parameter?->name }}</span>
                                <span class="text-[11px] text-gray-400">{{ $detail->parameter?->description }}</span>
                            </td>
                            <td class="text-center whitespace-nowrap font-mono font-semibold text-gray-800">
                                {{ number_format($detail->score, 1) }}
                            </td>
                            <td class="text-center whitespace-nowrap">
                                <span class="badge border border-gray-200 bg-gray-50 font-mono text-gray-700">
                                    {{ $weight }}%
                                </span>
                            </td>
                            <td class="text-center whitespace-nowrap font-mono font-semibold text-emerald-800">
                                {{ number_format($weighted, 2) }}
                            </td>
                            <td class="text-xs text-gray-600">
                                {{ $detail->notes ?: '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold text-gray-900 border-t border-gray-200">
                    <tr>
                        <td class="uppercase text-xs tracking-wider">Total Skor Terbobot</td>
                        <td class="text-center">-</td>
                        <td class="text-center font-mono">{{ $totalWeight }}%</td>
                        <td class="text-center font-mono text-sm text-emerald-900">
                            {{ number_format($totalWeighted, 2) }}
                        </td>
                        <td class="text-xs text-gray-500">Skor Final Tercatat: {{ number_format($evaluation->final_score, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Tindak Lanjut & Arahan Pembinaan --}}
    <div class="bg-white border border-gray-200 p-6 space-y-4">
        <div class="border-b border-gray-200 pb-3">
            <h3 class="font-semibold text-gray-900 text-xs uppercase tracking-wider">Panduan Tindak Lanjut Pembinaan (SOP Koperasi BMI)</h3>
            <p class="text-xs text-gray-500">Langkah operasional yang wajib dijalankan oleh staf pendamping</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div class="p-4 border {{ $evaluation->recommendation?->value === 'lanjutkan_tingkatkan' ? 'border-emerald-700 bg-emerald-50' : 'border-gray-200 bg-gray-50' }}">
                <span class="font-semibold text-emerald-900 text-xs uppercase tracking-wider block">1. Lanjutkan & Tingkatkan (>= 80)</span>
                <p class="text-gray-700 mt-2 leading-relaxed text-[11px]">
                    Usaha dinilai sehat dan berpotensi tinggi. Anggota diprioritaskan untuk penambahan plafond pembiayaan, ekspansi pasar, atau pendampingan sertifikasi halal/legalitas usaha.
                </p>
            </div>
            <div class="p-4 border {{ $evaluation->recommendation?->value === 'pembinaan_khusus' ? 'border-amber-600 bg-amber-50' : 'border-gray-200 bg-gray-50' }}">
                <span class="font-semibold text-amber-900 text-xs uppercase tracking-wider block">2. Pembinaan Khusus (60 - 79.9)</span>
                <p class="text-gray-700 mt-2 leading-relaxed text-[11px]">
                    Terdapat kelemahan pada pencatatan keuangan, kepatuhan angsuran, atau strategi penjualan. Jadwalkan kunjungan intensif dwimingguan dan pelatihan dasar keuangan syariah.
                </p>
            </div>
            <div class="p-4 border {{ $evaluation->recommendation?->value === 'hentikan' ? 'border-red-600 bg-red-50' : 'border-gray-200 bg-gray-50' }}">
                <span class="font-semibold text-red-900 text-xs uppercase tracking-wider block">3. Hentikan Pembiayaan (&lt; 60)</span>
                <p class="text-gray-700 mt-2 leading-relaxed text-[11px]">
                    Tingkat risiko wanprestasi tinggi atau usaha mengalami stagnasi berat. Pembekuan tambahan pinjaman baru, restrukturisasi sisa angsuran, dan mitigasi risiko kerugian.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
