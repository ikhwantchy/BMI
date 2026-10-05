<div x-data="{ step: 1 }" class="max-w-2xl mx-auto py-4">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-900">Form Pelaksanaan Kunjungan</h2>
        <p class="text-xs text-gray-500">Isi seluruh tahapan kunjungan di bawah ini.</p>
    </div>

    <!-- Stepper Navigation -->
    <div class="flex items-center justify-between mb-8 relative">
        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-gray-200 -z-10"></div>
        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-emerald-500 -z-10 transition-all duration-300" :style="`width: ${(step-1) * 33.33}%`"></div>
        
        <template x-for="i in 4">
            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs border-2 transition-colors"
                 :class="step >= i ? 'bg-emerald-500 border-emerald-500 text-white' : 'bg-white border-gray-300 text-gray-400'">
                <span x-text="i"></span>
            </div>
        </template>
    </div>

    <form action="{{ route('visits.submit_execution', $visit) }}" method="POST">
        @csrf

        <!-- STEP 1: Info & Catatan Lapangan -->
        <div x-show="step === 1" x-transition>
            <div class="bg-white border border-gray-200 p-5 mb-4 shadow-sm">
                <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">1. Informasi & Temuan Lapangan</h3>
                
                <div class="bg-blue-50 border border-blue-100 p-3 rounded mb-4 text-xs text-blue-900">
                    <p><strong>Anggota:</strong> {{ $visit->business->member->full_name }}</p>
                    <p><strong>Unit Usaha:</strong> {{ $visit->business->name }}</p>
                    <p><strong>Periode:</strong> {{ $visit->evaluation_period }}</p>
                </div>

                <div class="space-y-3">
                    <label class="block text-xs font-semibold uppercase text-gray-700">Kondisi & Temuan Lapangan <span class="text-red-500">*</span></label>
                    <textarea name="field_notes" required rows="4" class="input-base w-full" placeholder="Kondisi usaha saat ini, kendala, aktivitas yang sedang berjalan...">{{ $visit->field_notes }}</textarea>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="button" @click="step = 2" class="btn-primary">Selanjutnya &rarr;</button>
            </div>
        </div>

        <!-- STEP 2: Evaluasi & Scoring -->
        <div x-show="step === 2" x-transition style="display: none;">
            <div class="bg-white border border-gray-200 p-5 mb-4 shadow-sm">
                <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">2. Evaluasi & Indikator</h3>
                
                <div class="space-y-6">
                    @foreach($parameters as $param)
                    <div class="border border-gray-100 p-4 bg-gray-50 rounded">
                        <label class="block font-semibold text-gray-800 mb-1 text-sm">{{ $param->name }}</label>
                        <p class="text-[11px] text-gray-500 mb-3">{{ $param->description }} (Bobot: {{ $param->weight }}%)</p>
                        
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="w-full sm:w-1/4">
                                <input type="number" name="scores[{{ $param->id }}]" required min="0" max="100" class="input-base w-full font-mono text-center" placeholder="0-100">
                            </div>
                            <div class="flex-1">
                                <input type="text" name="notes[{{ $param->id }}]" class="input-base w-full" placeholder="Catatan parameter ini (opsional)...">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-between">
                <button type="button" @click="step = 1" class="btn-secondary">&larr; Kembali</button>
                <button type="button" @click="step = 3" class="btn-primary">Selanjutnya &rarr;</button>
            </div>
        </div>

        <!-- STEP 3: Rekomendasi -->
        <div x-show="step === 3" x-transition style="display: none;">
            <div class="bg-white border border-gray-200 p-5 mb-4 shadow-sm">
                <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">3. Rekomendasi & Tindak Lanjut</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-2">Rekomendasi Utama <span class="text-red-500">*</span></label>
                        <select name="recommendation" required class="input-base w-full">
                            <option value="">-- Pilih Rekomendasi --</option>
                            <option value="recommended">Direkomendasikan (Usaha Sehat)</option>
                            <option value="continued_coaching">Perlu Pembinaan Lanjutan</option>
                            <option value="not_recommended">Tidak Direkomendasikan / Kritis</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-2">Target / Perbaikan Selanjutnya <span class="text-red-500">*</span></label>
                        <textarea name="recommendation_reason" required rows="3" class="input-base w-full" placeholder="Tindak lanjut yang harus dilakukan anggota..."></textarea>
                    </div>
                </div>
            </div>
            <div class="flex justify-between">
                <button type="button" @click="step = 2" class="btn-secondary">&larr; Kembali</button>
                <button type="button" @click="step = 4" class="btn-primary">Preview & Submit &rarr;</button>
            </div>
        </div>

        <!-- STEP 4: Submit -->
        <div x-show="step === 4" x-transition style="display: none;">
            <div class="bg-emerald-50 border border-emerald-200 p-5 mb-4 text-center rounded shadow-sm">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="font-bold text-emerald-900 text-lg mb-1">Siap Disubmit!</h3>
                <p class="text-xs text-emerald-700 mb-4">Pastikan Anda sudah mengunggah foto lapangan (di bawah form ini) sebelum klik Submit.</p>
                
                <button type="submit" class="btn-primary w-full sm:w-auto px-8 py-3 text-base shadow-md">
                    Kirim Hasil Kunjungan
                </button>
            </div>
            <div class="flex justify-start">
                <button type="button" @click="step = 3" class="btn-secondary">&larr; Kembali Cek Data</button>
            </div>
        </div>
    </form>

    <!-- Upload Foto Section (Tampil Terus di Bawah Form) -->
    <div class="mt-8 border-t-2 border-dashed border-gray-300 pt-6">
        <h3 class="font-bold text-gray-800 mb-3">Dokumentasi Lapangan</h3>
        @include('visits.partials.upload-form')
    </div>
</div>
