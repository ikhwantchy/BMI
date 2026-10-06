<div x-data="visitExecutionForm()" class="max-w-3xl mx-auto py-4">
    
    {{-- Header Banner & Revision Notice --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-200 pb-3">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Form Pelaksanaan Kunjungan</h2>
                <p class="text-xs text-gray-500">Koperasi Syariah Benteng Mikro Indonesia &bull; Evaluasi Pembinaan Usaha</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge border bg-{{ $visit->status->badgeColor() }}-50 border-{{ $visit->status->badgeColor() }}-200 text-{{ $visit->status->badgeColor() }}-800 font-semibold text-xs">
                    Status: {{ $visit->status->label() }}
                </span>
                <button type="button" @click="saveDraft()" :disabled="savingDraft"
                        class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1.5 shadow-xs border-emerald-300 text-emerald-800 hover:bg-emerald-50">
                    <svg class="w-3.5 h-3.5" :class="savingDraft ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    <span x-text="savingDraft ? 'Menyimpan...' : 'Simpan Draft'"></span>
                </button>
            </div>
        </div>

        {{-- Toast Notification Draft --}}
        <div x-show="toastMessage" x-transition class="mt-3 p-3 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-md text-xs flex items-center justify-between" style="display: none;">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span x-text="toastMessage"></span>
            </div>
            <button type="button" @click="toastMessage = ''" class="text-emerald-700 hover:text-emerald-900 font-bold">&times;</button>
        </div>

        {{-- Catatan Revisi dari Manajer (Jika Status NeedsRevision) --}}
        @if($visit->status->value === 'needs_revision' && $visit->evaluation?->validator_notes)
            <div class="mt-4 p-4 bg-orange-50 border-l-4 border-orange-500 rounded-r shadow-xs">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-orange-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-orange-900">Perhatian: Kunjungan Memerlukan Revisi</h4>
                        <p class="text-xs text-orange-800">
                            Catatan Manajer ({{ $visit->evaluation->validatedBy?->name ?? 'Manajer' }}):
                        </p>
                        <div class="text-xs text-orange-950 bg-white/80 p-3 rounded border border-orange-200 mt-1 whitespace-pre-line font-medium">
                            {{ $visit->evaluation->validator_notes }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Stepper Navigation -->
    <div class="mb-8">
        <div class="flex items-center justify-between relative mb-2">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-gray-200 -z-10"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-emerald-500 -z-10 transition-all duration-300" :style="`width: ${(step-1) * 25}%`"></div>
            
            <template x-for="(label, i) in stepLabels">
                <div class="flex flex-col items-center cursor-pointer group" @click="step = i + 1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs border-2 transition-colors shadow-xs"
                         :class="step >= i + 1 ? 'bg-emerald-600 border-emerald-600 text-white' : 'bg-white border-gray-300 text-gray-500'">
                        <span x-text="i + 1"></span>
                    </div>
                    <span class="text-[10px] font-semibold mt-1 hidden sm:block"
                          :class="step === i + 1 ? 'text-emerald-700' : 'text-gray-400'"
                          x-text="label"></span>
                </div>
            </template>
        </div>
    </div>

    <form id="executeForm" action="{{ route('visits.submit_execution', $visit) }}" method="POST">
        @csrf

        <!-- STEP 1: Informasi & Kondisi Lapangan -->
        <div x-show="step === 1" x-transition>
            <div class="bg-white border border-gray-200 p-5 mb-4 shadow-sm space-y-4">
                <div class="border-b pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">1. Informasi & Temuan Lapangan</h3>
                        <p class="text-[11px] text-gray-500">Catat kondisi faktual usaha anggota saat kunjungan berlangsung.</p>
                    </div>
                    <span class="text-[10px] font-mono text-gray-400">Tahap 1 dari 5</span>
                </div>
                
                {{-- Info Anggota & Usaha (Readonly context) --}}
                <div class="bg-emerald-50/70 border border-emerald-200 p-3.5 rounded text-xs text-emerald-950 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div>
                        <span class="text-[10px] text-emerald-700 block uppercase font-semibold">Anggota</span>
                        <span class="font-bold">{{ $visit->business->member->full_name }}</span> ({{ $visit->business->member->member_number }})
                    </div>
                    <div>
                        <span class="text-[10px] text-emerald-700 block uppercase font-semibold">Unit Usaha</span>
                        <span class="font-bold">{{ $visit->business->name }}</span> ({{ $visit->business->business_type }})
                    </div>
                    <div>
                        <span class="text-[10px] text-emerald-700 block uppercase font-semibold">Tanggal & Periode</span>
                        <span class="font-bold">{{ $visit->visit_date->translatedFormat('d M Y') }}</span> &bull; <span class="font-mono">{{ $visit->evaluation_period }}</span>
                    </div>
                </div>

                {{-- 1.1 Kondisi Usaha --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-800 uppercase tracking-wider">
                        1.1 Kondisi Tempat Usaha <span class="text-red-500">*</span>
                    </label>
                    <textarea name="business_condition" x-model="formData.business_condition" required rows="2" class="input-base w-full text-xs"
                              placeholder="Deskripsi fisik tempat usaha, kebersihan, display produk, fasilitas pendukung..."></textarea>
                </div>

                {{-- 1.2 Aktivitas Usaha --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-800 uppercase tracking-wider">
                        1.2 Aktivitas & Operasional Usaha <span class="text-red-500">*</span>
                    </label>
                    <textarea name="business_activity" x-model="formData.business_activity" required rows="2" class="input-base w-full text-xs"
                              placeholder="Jam buka-tutup, kesibukan transaksi, jumlah pelanggan harian, tenaga kerja..."></textarea>
                </div>

                {{-- 1.3 Perkembangan Omzet / Penjualan --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-800 uppercase tracking-wider">
                        1.3 Perkembangan Penjualan / Omzet <span class="text-red-500">*</span>
                    </label>
                    <textarea name="revenue_trend" x-model="formData.revenue_trend" required rows="2" class="input-base w-full text-xs"
                              placeholder="Estimasi omzet harian/bulanan saat ini, perbandingan dengan bulan lalu, tren kenaikan/penurunan..."></textarea>
                </div>

                {{-- 1.4 Kendala Usaha --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-800 uppercase tracking-wider">
                        1.4 Kendala & Hambatan Lapangan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="business_obstacles" x-model="formData.business_obstacles" required rows="2" class="input-base w-full text-xs"
                              placeholder="Kendala bahan baku, persaingan, pengelolaan kas, piutang macet pelanggan, atau masalah cuaca..."></textarea>
                </div>

                {{-- 1.5 Temuan Khusus & Observasi --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-800 uppercase tracking-wider">
                        1.5 Temuan Khusus Lapangan
                    </label>
                    <textarea name="field_findings" x-model="formData.field_findings" rows="2" class="input-base w-full text-xs"
                              placeholder="Temuan penting lainnya seperti kepatuhan syariah, buku catatan harian, aset baru, dll..."></textarea>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="button" @click="saveDraft()" class="btn-secondary text-xs">Simpan Draft</button>
                <button type="button" @click="step = 2" class="btn-primary text-xs">Selanjutnya: Evaluasi & Skor &rarr;</button>
            </div>
        </div>

        <!-- STEP 2: Evaluasi & Scoring Parameter -->
        <div x-show="step === 2" x-transition style="display: none;">
            <div class="bg-white border border-gray-200 p-5 mb-4 shadow-sm space-y-4">
                <div class="border-b pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">2. Evaluasi & Penilaian Parameter (Weighted Scoring)</h3>
                        <p class="text-[11px] text-gray-500">Beri skor 0 - 100 pada setiap parameter sesuai kondisi lapangan.</p>
                    </div>
                    <span class="text-[10px] font-mono text-gray-400">Tahap 2 dari 5</span>
                </div>

                {{-- Live Scoring Card Preview --}}
                <div class="p-3.5 bg-gray-50 border border-emerald-300 rounded flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Estimasi Skor Terbobot</span>
                        <div class="flex items-center gap-3 mt-0.5">
                            <span class="text-2xl font-bold font-mono text-emerald-900" x-text="calculateTotalScore().toFixed(1)"></span>
                            <span class="text-xs px-2.5 py-0.5 font-semibold rounded-full border"
                                  :class="getRecommendationBadgeClass()">
                                <span x-text="getRecommendationLabel()"></span>
                            </span>
                        </div>
                    </div>
                    <div class="text-[11px] text-gray-500">
                        Formula resmi Kopsyah BMI:<br>
                        <span class="font-mono text-gray-700">&ge; 80 (Direkomendasikan), 60-79 (Pembinaan Lanjutan), &lt; 60 (Tidak Direkomendasikan)</span>
                    </div>
                </div>

                {{-- Parameters List --}}
                <div class="space-y-4">
                    @php
                        $existingDetails = $visit->evaluation?->details->keyBy('parameter_id') ?? collect();
                    @endphp
                    @foreach($parameters as $param)
                    @php
                        $oldDetail = $existingDetails->get($param->id);
                        $paramWeight = $param->weight_percent ?? ($param->weight <= 1 ? $param->weight * 100 : $param->weight);
                    @endphp
                    <div class="border border-gray-200 p-4 bg-gray-50/50 rounded hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <label class="font-bold text-gray-900 text-xs block">{{ $param->name }}</label>
                                <p class="text-[11px] text-gray-500 mt-0.5">{{ $param->description }}</p>
                            </div>
                            <span class="badge border border-emerald-200 bg-emerald-50 text-emerald-800 font-mono text-[11px] font-semibold shrink-0">
                                Bobot: {{ $paramWeight }}%
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mt-3">
                            <div class="sm:col-span-1">
                                <label class="block text-[10px] uppercase font-semibold text-gray-600 mb-1">Nilai (0 - 100) <span class="text-red-500">*</span></label>
                                <input type="number" name="scores[{{ $param->id }}]"
                                       x-model.number="formData.scores[{{ $param->id }}]"
                                       required min="0" max="100"
                                       class="input-base w-full font-mono text-center font-bold text-sm" placeholder="0-100">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-[10px] uppercase font-semibold text-gray-600 mb-1">Catatan Temuan Parameter Ini (Opsional)</label>
                                <input type="text" name="notes[{{ $param->id }}]"
                                       x-model="formData.notes[{{ $param->id }}]"
                                       class="input-base w-full text-xs" placeholder="Temuan terkait {{ strtolower($param->name) }}...">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="button" @click="step = 1" class="btn-secondary text-xs">&larr; Kembali</button>
                <div class="flex items-center gap-2">
                    <button type="button" @click="saveDraft()" class="btn-secondary text-xs">Simpan Draft</button>
                    <button type="button" @click="step = 3" class="btn-primary text-xs">Selanjutnya: Dokumentasi &rarr;</button>
                </div>
            </div>
        </div>
    </form>

    <!-- STEP 3: Dokumentasi Lapangan (AJAX Form) -->
    <div x-show="step === 3" x-transition style="display: none;">
        <div class="bg-white border border-gray-200 p-5 mb-4 shadow-sm space-y-4">
            <div class="border-b pb-3 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">3. Dokumentasi Foto Lapangan</h3>
                    <p class="text-[11px] text-gray-500">Ambil foto tempat usaha menggunakan kamera smartphone (auto GPS & time-stamp watermark).</p>
                </div>
                <span class="text-[10px] font-mono text-gray-400">Tahap 3 dari 5</span>
            </div>

            @include('visits.partials.upload-form')
        </div>

        <div class="flex items-center justify-between">
            <button type="button" @click="step = 2" class="btn-secondary text-xs">&larr; Kembali</button>
            <button type="button" @click="step = 4" class="btn-primary text-xs">Selanjutnya: Rekomendasi &rarr;</button>
        </div>
    </div>

    <!-- STEP 4: Rekomendasi & Tindak Lanjut -->
    <div x-show="step === 4" x-transition style="display: none;">
        <div class="bg-white border border-gray-200 p-5 mb-4 shadow-sm space-y-4">
            <div class="border-b pb-3 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">4. Rekomendasi & Tindak Lanjut Pembinaan</h3>
                    <p class="text-[11px] text-gray-500">Rumuskan rekomendasi pembinaan dan arahan perbaikan bagi anggota.</p>
                </div>
                <span class="text-[10px] font-mono text-gray-400">Tahap 4 dari 5</span>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-800 mb-1">
                        4.1 Rekomendasi Utama <span class="text-red-500">*</span>
                    </label>
                    <select form="executeForm" name="recommendation" x-model="formData.recommendation" required class="input-base w-full text-xs font-semibold">
                        <option value="">-- Pilih Rekomendasi --</option>
                        <option value="recommended">✅ Direkomendasikan (Usaha Sehat & Berkelanjutan)</option>
                        <option value="continued_coaching">⚠️ Perlu Pembinaan Lanjutan (Memerlukan Pendampingan Khusus)</option>
                        <option value="not_recommended">❌ Tidak Direkomendasikan (Risiko Tinggi / Perlu Evaluasi Mendalam)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-800 mb-1">
                        4.2 Alasan & Justifikasi Rekomendasi
                    </label>
                    <textarea form="executeForm" name="recommendation_reason" x-model="formData.recommendation_reason" rows="2" class="input-base w-full text-xs"
                              placeholder="Dasar pertimbangan pemberian rekomendasi di atas..."></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-800 mb-1">
                        4.3 Rencana Tindak Lanjut Pembinaan
                    </label>
                    <textarea form="executeForm" name="action_plan" x-model="formData.action_plan" rows="2" class="input-base w-full text-xs"
                              placeholder="Langkah pendampingan yang akan dilakukan oleh petugas (misal: pelatihan pencatatan kas harian)..."></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-800 mb-1">
                        4.4 Target & Komitmen Perbaikan Anggota
                    </label>
                    <textarea form="executeForm" name="improvement_target" x-model="formData.improvement_target" rows="2" class="input-base w-full text-xs"
                              placeholder="Target yang harus dicapai anggota sebelum evaluasi berikutnya (misal: pisahkan rekening pribadi dan usaha)..."></textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <button type="button" @click="step = 3" class="btn-secondary text-xs">&larr; Kembali</button>
            <div class="flex items-center gap-2">
                <button type="button" @click="saveDraft()" class="btn-secondary text-xs">Simpan Draft</button>
                <button type="button" @click="step = 5" class="btn-primary text-xs">Review & Ringkasan &rarr;</button>
            </div>
        </div>
    </div>

    <!-- STEP 5: Review Ringkasan & Submit -->
    <div x-show="step === 5" x-transition style="display: none;">
        <div class="bg-white border border-gray-200 p-5 mb-4 shadow-sm space-y-5">
            <div class="border-b pb-3 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">5. Ringkasan Kunjungan & Validasi Akhir</h3>
                    <p class="text-[11px] text-gray-500">Periksa kembali seluruh informasi sebelum mengirimkan laporan ke manajer.</p>
                </div>
                <span class="text-[10px] font-mono text-gray-400">Tahap 5 dari 5</span>
            </div>

            {{-- Ringkasan Identitas --}}
            <div class="bg-gray-50 border border-gray-200 p-4 rounded text-xs space-y-2">
                <h4 class="font-bold text-gray-800 uppercase tracking-wider text-[11px] border-b pb-1">Identitas Kunjungan</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                    <p><span class="text-gray-500">Anggota:</span> <strong class="text-gray-900">{{ $visit->business->member->full_name }}</strong> ({{ $visit->business->member->member_number }})</p>
                    <p><span class="text-gray-500">Unit Usaha:</span> <strong class="text-gray-900">{{ $visit->business->name }}</strong></p>
                    <p><span class="text-gray-500">Tanggal Kunjungan:</span> <strong class="text-gray-900">{{ $visit->visit_date->translatedFormat('d F Y') }}</strong></p>
                    <p><span class="text-gray-500">Periode Evaluasi:</span> <strong class="font-mono text-gray-900">{{ $visit->evaluation_period }}</strong></p>
                </div>
            </div>

            {{-- Ringkasan Temuan Lapangan --}}
            <div class="border border-gray-200 p-4 rounded text-xs space-y-2">
                <h4 class="font-bold text-gray-800 uppercase tracking-wider text-[11px] border-b pb-1">Temuan Lapangan Terstruktur</h4>
                <div class="space-y-1.5 mt-2">
                    <p><span class="font-semibold text-gray-600">Kondisi Tempat Usaha:</span> <span class="text-gray-800" x-text="formData.business_condition || '-'"></span></p>
                    <p><span class="font-semibold text-gray-600">Aktivitas Operasional:</span> <span class="text-gray-800" x-text="formData.business_activity || '-'"></span></p>
                    <p><span class="font-semibold text-gray-600">Perkembangan Omzet:</span> <span class="text-gray-800" x-text="formData.revenue_trend || '-'"></span></p>
                    <p><span class="font-semibold text-gray-600">Kendala Lapangan:</span> <span class="text-gray-800" x-text="formData.business_obstacles || '-'"></span></p>
                    <template x-if="formData.field_findings">
                        <p><span class="font-semibold text-gray-600">Temuan Khusus:</span> <span class="text-gray-800" x-text="formData.field_findings"></span></p>
                    </template>
                </div>
            </div>

            {{-- Ringkasan Nilai Parameter --}}
            <div class="border border-gray-200 p-4 rounded text-xs space-y-2">
                <div class="flex items-center justify-between border-b pb-1">
                    <h4 class="font-bold text-gray-800 uppercase tracking-wider text-[11px]">Rincian Penilaian Parameter</h4>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500">Total Skor:</span>
                        <span class="font-bold font-mono text-emerald-800 text-sm" x-text="calculateTotalScore().toFixed(1)"></span>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                    @foreach($parameters as $param)
                    @php
                        $paramWeight = $param->weight_percent ?? ($param->weight <= 1 ? $param->weight * 100 : $param->weight);
                    @endphp
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-100">
                        <span class="text-gray-600 font-medium">{{ $param->name }} ({{ $paramWeight }}%):</span>
                        <span class="font-mono font-bold text-gray-900" x-text="(formData.scores[{{ $param->id }}] ?? '-') + ' / 100'"></span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Ringkasan Rekomendasi & Tindak Lanjut --}}
            <div class="border border-gray-200 p-4 rounded text-xs space-y-2 bg-emerald-50/40">
                <h4 class="font-bold text-emerald-900 uppercase tracking-wider text-[11px] border-b border-emerald-200 pb-1">Rekomendasi & Tindak Lanjut</h4>
                <div class="space-y-1.5 mt-2">
                    <p><span class="font-semibold text-gray-600">Rekomendasi:</span> <strong class="text-emerald-950" x-text="getRecommendationLabel()"></strong></p>
                    <template x-if="formData.recommendation_reason">
                        <p><span class="font-semibold text-gray-600">Alasan:</span> <span class="text-gray-800" x-text="formData.recommendation_reason"></span></p>
                    </template>
                    <template x-if="formData.action_plan">
                        <p><span class="font-semibold text-gray-600">Rencana Pembinaan:</span> <span class="text-gray-800" x-text="formData.action_plan"></span></p>
                    </template>
                    <template x-if="formData.improvement_target">
                        <p><span class="font-semibold text-gray-600">Target Perbaikan:</span> <span class="text-gray-800" x-text="formData.improvement_target"></span></p>
                    </template>
                </div>
            </div>

            {{-- Submit Callout Card --}}
            <div class="p-4 bg-emerald-100/70 border border-emerald-300 rounded text-center space-y-2">
                <h4 class="font-bold text-emerald-950 text-sm">Laporan Siap Diajukan</h4>
                <p class="text-xs text-emerald-800 max-w-lg mx-auto">
                    Setelah Anda mengirim laporan ini, status kunjungan akan berubah menjadi <strong>Menunggu Validasi</strong> untuk diperiksa dan disetujui oleh Manajer Cabang.
                </p>
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <button type="button" @click="saveDraft()" class="btn-secondary text-xs w-full sm:w-auto">
                        Simpan Sebagai Draft
                    </button>
                    <button type="submit" form="executeForm" onclick="sessionStorage.removeItem('visit_step_{{ $visit->id }}')"
                            class="btn-primary text-xs w-full sm:w-auto px-6 py-2.5 font-bold shadow-md">
                        🚀 Kirim Hasil Kunjungan ke Manajer
                    </button>
                </div>
            </div>
        </div>

        <div class="flex justify-start">
            <button type="button" @click="step = 4" class="btn-secondary text-xs">&larr; Kembali Cek Data</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function visitExecutionForm() {
    return {
        step: sessionStorage.getItem('visit_step_{{ $visit->id }}') ? parseInt(sessionStorage.getItem('visit_step_{{ $visit->id }}')) : 1,
        stepLabels: ['1. Temuan Lapangan', '2. Evaluasi Skor', '3. Dokumentasi', '4. Rekomendasi', '5. Review & Submit'],
        savingDraft: false,
        toastMessage: '',
        parameters: @json($parameters->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'weight' => $p->weight_percent ?? ($p->weight <= 1 ? $p->weight * 100 : $p->weight)
        ])),
        formData: {
            business_condition: @json($visit->business_condition ?? ''),
            business_activity: @json($visit->business_activity ?? ''),
            revenue_trend: @json($visit->revenue_trend ?? ''),
            business_obstacles: @json($visit->business_obstacles ?? ''),
            field_findings: @json($visit->field_findings ?? $visit->field_notes ?? ''),
            field_notes: @json($visit->field_notes ?? ''),
            recommendation: @json($visit->evaluation?->recommendation?->value ?? ($visit->evaluation?->recommendation ?? '')),
            recommendation_reason: @json($visit->evaluation?->recommendation_reason ?? ''),
            action_plan: @json($visit->action_plan ?? ''),
            improvement_target: @json($visit->improvement_target ?? ''),
            scores: {
                @foreach($parameters as $param)
                {{ $param->id }}: @json($visit->evaluation?->details->where('parameter_id', $param->id)->first()?->score ?? null),
                @endforeach
            },
            notes: {
                @foreach($parameters as $param)
                {{ $param->id }}: @json($visit->evaluation?->details->where('parameter_id', $param->id)->first()?->notes ?? ''),
                @endforeach
            }
        },

        init() {
            this.$watch('step', val => sessionStorage.setItem('visit_step_{{ $visit->id }}', val));
        },

        calculateTotalScore() {
            let total = 0;
            let sumWeight = 0;
            this.parameters.forEach(p => {
                const sc = parseFloat(this.formData.scores[p.id]);
                const weight = parseFloat(p.weight) || 20;
                if (!isNaN(sc) && sc >= 0) {
                    total += (sc * weight) / 100;
                    sumWeight += weight;
                }
            });
            return total;
        },

        getRecommendationLabel() {
            const sc = this.calculateTotalScore();
            if (this.formData.recommendation === 'recommended' || (sc >= 80 && !this.formData.recommendation)) {
                return 'Direkomendasikan (>= 80)';
            } else if (this.formData.recommendation === 'continued_coaching' || (sc >= 60 && !this.formData.recommendation)) {
                return 'Pembinaan Lanjutan (60 - 79)';
            } else if (this.formData.recommendation === 'not_recommended' || (sc > 0 && sc < 60 && !this.formData.recommendation)) {
                return 'Tidak Direkomendasikan (< 60)';
            }
            return 'Belum Ditentukan';
        },

        getRecommendationBadgeClass() {
            const sc = this.calculateTotalScore();
            if (this.formData.recommendation === 'recommended' || sc >= 80) {
                return 'bg-emerald-50 text-emerald-800 border-emerald-300';
            } else if (this.formData.recommendation === 'continued_coaching' || (sc >= 60 && sc < 80)) {
                return 'bg-amber-50 text-amber-800 border-amber-300';
            }
            return 'bg-red-50 text-red-800 border-red-300';
        },

        async saveDraft() {
            this.savingDraft = true;
            this.toastMessage = '';

            try {
                const fd = new FormData();
                fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                fd.append('business_condition', this.formData.business_condition || '');
                fd.append('business_activity', this.formData.business_activity || '');
                fd.append('revenue_trend', this.formData.revenue_trend || '');
                fd.append('business_obstacles', this.formData.business_obstacles || '');
                fd.append('field_findings', this.formData.field_findings || '');
                fd.append('field_notes', this.formData.field_findings || '');
                fd.append('action_plan', this.formData.action_plan || '');
                fd.append('improvement_target', this.formData.improvement_target || '');
                fd.append('recommendation', this.formData.recommendation || '');
                fd.append('recommendation_reason', this.formData.recommendation_reason || '');

                for (let k in this.formData.scores) {
                    if (this.formData.scores[k] !== null && this.formData.scores[k] !== '') {
                        fd.append(`scores[${k}]`, this.formData.scores[k]);
                    }
                }
                for (let k in this.formData.notes) {
                    if (this.formData.notes[k]) {
                        fd.append(`notes[${k}]`, this.formData.notes[k]);
                    }
                }

                const res = await fetch("{{ route('visits.save_draft', $visit) }}", {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    const data = await res.json();
                    this.toastMessage = data.message || 'Draft berhasil disimpan secara otomatis.';
                    setTimeout(() => { this.toastMessage = ''; }, 4000);
                } else {
                    alert('Gagal menyimpan draft ke server.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan saat menyimpan draft.');
            } finally {
                this.savingDraft = false;
            }
        }
    };
}
</script>
@endpush
