@extends('layouts.app')

@section('title', 'Input Evaluasi: ' . $visit->business->name)
@section('page-title', 'Form Evaluasi Usaha')

@section('content')
<div class="py-4 space-y-6"
     x-data="{
         scores: {
             @foreach($parameters as $param)
                 '{{ $param->id }}': {{ old('scores.' . $param->id, 70) }},
             @endforeach
         },
         weights: {
             @foreach($parameters as $param)
                 '{{ $param->id }}': {{ $param->weight_percent }},
             @endforeach
         },
         calculateTotal() {
             let total = 0;
             for (const [id, weight] of Object.entries(this.weights)) {
                 const score = parseFloat(this.scores[id]) || 0;
                 total += (score * weight / 100);
             }
             return total.toFixed(1);
         },
         getRecommendation(total) {
             const score = parseFloat(total);
             if (score >= 80) return { label: 'Lanjutkan & Tingkatkan Pembiayaan', color: 'bg-emerald-50 border-emerald-300 text-emerald-900' };
             if (score >= 60) return { label: 'Pembinaan Khusus', color: 'bg-amber-50 border-amber-300 text-amber-900' };
             return { label: 'Hentikan Pembiayaan', color: 'bg-red-50 border-red-300 text-red-900' };
         }
     }">

    {{-- Breadcrumb & Top Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('visits.index') }}" class="hover:text-emerald-700">Kunjungan</a>
                <span>/</span>
                <a href="{{ route('visits.show', $visit) }}" class="hover:text-emerald-700">{{ $visit->business->name }}</a>
                <span>/</span>
                <span class="text-gray-900 font-semibold">Input Evaluasi</span>
            </div>
            <h2 class="text-base font-semibold text-gray-900">Lembar Penilaian Evaluasi Usaha</h2>
            <p class="text-xs text-gray-500 mt-0.5">Kunjungan: {{ $visit->visit_date->translatedFormat('d F Y') }} &bull; Periode: {{ $visit->evaluation_period }}</p>
        </div>
        <a href="{{ route('visits.show', $visit) }}" class="btn-secondary text-xs">
            &larr; Kembali
        </a>
    </div>

    {{-- Live Calculation Sticky Bar --}}
    <div class="sticky top-2 z-10 bg-white border border-gray-300 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-10 h-10 bg-emerald-800 text-white flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0l-4-4m4 4l4-4M3 9l9-7 9 7"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider block">Estimasi Skor Terbobot</span>
                <div class="flex items-baseline space-x-2 font-mono">
                    <span class="text-2xl font-semibold text-gray-900" x-text="calculateTotal()">70.0</span>
                    <span class="text-xs text-gray-400">/ 100</span>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3 text-xs">
            <span class="text-gray-500 hidden sm:inline uppercase tracking-wider text-[10px] font-semibold">Prediksi Rekomendasi:</span>
            <span class="badge border py-1.5 px-3"
                  :class="getRecommendation(calculateTotal()).color"
                  x-text="getRecommendation(calculateTotal()).label">
                Pembinaan Khusus
            </span>
        </div>
    </div>

    {{-- Form Penilaian --}}
    <form action="{{ route('evaluations.store', $visit) }}" method="POST" class="space-y-6">
        @csrf

        @foreach($parameters as $index => $param)
            <div class="bg-white border border-gray-200 p-6 space-y-4">
                {{-- Parameter Header --}}
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 pb-3 border-b border-gray-100">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="w-5 h-5 bg-gray-100 text-gray-800 text-xs font-semibold flex items-center justify-center font-mono">
                                {{ $index + 1 }}
                            </span>
                            <h3 class="text-sm font-semibold text-gray-900">{{ $param->name }}</h3>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 pl-7">{{ $param->description }}</p>
                    </div>
                    <div class="pl-7 sm:pl-0 flex items-center space-x-2 text-xs">
                        <span class="badge border border-emerald-200 bg-emerald-50 text-emerald-800 font-mono">
                            Bobot: {{ $param->weight_percent }}%
                        </span>
                        <span class="font-mono text-gray-700">
                            Subtotal: <span class="font-semibold" x-text="((scores['{{ $param->id }}'] || 0) * {{ $param->weight_percent }} / 100).toFixed(1)"></span> pt
                        </span>
                    </div>
                </div>

                {{-- Indikator Penilaian Panduan --}}
                @if($param->indicators->isNotEmpty())
                    <div class="bg-gray-50 p-3.5 border border-gray-200 text-xs space-y-1.5">
                        <span class="font-semibold text-gray-600 block text-[10px] uppercase tracking-wider">Kriteria & Indikator Acuan Lapangan:</span>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-gray-700">
                            @foreach($param->indicators as $ind)
                                <div class="flex items-start space-x-1.5 text-xs">
                                    <span class="text-emerald-700 font-semibold">&bull;</span>
                                    <span><strong class="font-medium text-gray-900">{{ $ind->name }}</strong>: {{ $ind->scoring_guide ?: 'Sesuai amatan lapangan' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Input Nilai & Slider --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 items-center">
                    <div class="sm:col-span-2 space-y-2">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700">
                            Geser atau Ketik Nilai (Skala 0 - 100)
                        </label>
                        <input type="range"
                               min="0"
                               max="100"
                               step="1"
                               x-model="scores['{{ $param->id }}']"
                               class="w-full h-1.5 bg-gray-200 appearance-none cursor-pointer accent-emerald-700">
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <input type="number"
                                   name="scores[{{ $param->id }}]"
                                   min="0"
                                   max="100"
                                   step="1"
                                   required
                                   x-model="scores['{{ $param->id }}']"
                                   class="input-base text-center font-mono font-semibold text-sm">
                            <span class="text-xs text-gray-400 font-mono">/100</span>
                        </div>
                        @error('scores.' . $param->id)
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Catatan Khusus Parameter --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">
                        Catatan Temuan untuk Parameter Ini (Opsional)
                    </label>
                    <input type="text"
                           name="notes[{{ $param->id }}]"
                           value="{{ old('notes.' . $param->id) }}"
                           placeholder="Catatan temuan khusus terkait {{ strtolower($param->name) }}..."
                           class="input-base">
                </div>
            </div>
        @endforeach

        {{-- Action Buttons --}}
        <div class="bg-white border border-gray-200 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="text-xs text-gray-500">
                Hasil evaluasi akan otomatis disimpan sebagai <strong>Draft</strong> dan skor dihitung berdasarkan rumus formula pembobotan sistem.
            </div>
            <div class="flex items-center space-x-3 self-end sm:self-center">
                <a href="{{ route('visits.show', $visit) }}" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    Simpan Evaluasi & Hitung Skor
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
