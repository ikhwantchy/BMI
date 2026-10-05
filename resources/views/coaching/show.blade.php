@extends('layouts.app')

@section('title', 'Detail Pembinaan: ' . $coaching->title)
@section('page-title', 'Detail Rekomendasi Pembinaan')

@section('content')
<div class="py-4 space-y-6">

    {{-- Breadcrumb & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 pb-4">
        <div class="flex items-center space-x-2 text-xs text-gray-500">
            <a href="{{ route('coaching.index') }}" class="hover:text-emerald-700 transition">Pembinaan</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold truncate max-w-xs">{{ $coaching->title }}</span>
        </div>
        <div class="flex items-center gap-2">
            @if($coaching->status->value === 'pending')
                <a href="{{ route('coaching.edit', $coaching) }}" class="btn-secondary">
                    Edit Rekomendasi
                </a>
            @endif
        </div>
    </div>

    {{-- Header Card --}}
    <div class="bg-white border border-gray-200">
        <div class="bg-emerald-900 px-6 py-5 text-white">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <span class="badge border bg-white/10 border-white/20 text-white mb-2">
                        {{ $coaching->category }}
                    </span>
                    <h3 class="text-base font-semibold leading-snug">{{ $coaching->title }}</h3>
                    <p class="text-emerald-200 text-xs mt-1">
                        Usaha:
                        <a href="{{ route('businesses.show', $coaching->evaluation->business) }}"
                           class="font-medium underline text-white hover:text-emerald-100">
                            {{ $coaching->evaluation->business->name }}
                        </a>
                        &mdash; {{ $coaching->evaluation->business->member->full_name }}
                    </p>
                </div>
                <div>
                    <span class="badge border
                        {{ $coaching->status->value === 'done' ? 'bg-emerald-800 border-emerald-700 text-white' :
                           ($coaching->status->value === 'in_progress' ? 'bg-blue-800 border-blue-700 text-white' : 'bg-amber-800 border-amber-700 text-white') }}">
                        Status: {{ $coaching->status->label() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Deskripsi Pembinaan</dt>
                <dd class="mt-2 text-sm text-gray-800 bg-gray-50 p-4 border border-gray-200 leading-relaxed">
                    {{ $coaching->description }}
                </dd>
            </div>
            @if($coaching->reason)
                <div>
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Alasan / Latar Belakang</dt>
                    <dd class="mt-2 text-sm text-gray-800 bg-gray-50 p-4 border border-gray-200 leading-relaxed">
                        {{ $coaching->reason }}
                    </dd>
                </div>
            @endif

            {{-- Meta Info --}}
            <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-gray-200">
                <div>
                    <dt class="text-[10px] uppercase font-semibold text-gray-400">Dibuat Oleh</dt>
                    <dd class="text-xs font-semibold text-gray-800 mt-0.5">{{ $coaching->createdBy?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-[10px] uppercase font-semibold text-gray-400">Tanggal Dibuat</dt>
                    <dd class="text-xs font-semibold text-gray-800 mt-0.5 font-mono">{{ $coaching->created_at->translatedFormat('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-[10px] uppercase font-semibold text-gray-400">Evaluasi Terkait</dt>
                    <dd class="text-xs mt-0.5">
                        <a href="{{ route('evaluations.show', $coaching->evaluation) }}" class="font-semibold text-emerald-700 hover:underline">
                            Lihat Evaluasi &rarr;
                        </a>
                    </dd>
                </div>
                <div>
                    <dt class="text-[10px] uppercase font-semibold text-gray-400">Periode Evaluasi</dt>
                    <dd class="text-xs font-semibold font-mono text-gray-800 mt-0.5">
                        {{ $coaching->evaluation->visit?->evaluation_period ?? '-' }}
                    </dd>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Tindak Lanjut & Form Tambah --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Riwayat Tindak Lanjut --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 p-6 space-y-4">
            <div class="border-b border-gray-200 pb-3">
                <h3 class="font-semibold text-gray-900 text-xs uppercase tracking-wider">Riwayat Tindak Lanjut</h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Catatan kegiatan pendampingan yang telah dilaksanakan</p>
            </div>

            @if($coaching->followups->isEmpty())
                <div class="py-10 text-center text-gray-400">
                    <p class="text-xs text-gray-500">Belum ada catatan tindak lanjut kegiatan pembinaan.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($coaching->followups->sortByDesc('followup_date') as $followup)
                        <div class="p-4 border border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-emerald-700"></span>
                                    <span class="text-xs font-semibold text-gray-800 font-mono">
                                        {{ \Carbon\Carbon::parse($followup->followup_date)->translatedFormat('d F Y') }}
                                    </span>
                                </div>
                                <span class="text-[11px] text-gray-500">{{ $followup->officer?->name }}</span>
                            </div>
                            <p class="text-xs text-gray-900 font-medium">{{ $followup->activity }}</p>
                            @if($followup->result)
                                <p class="text-xs text-gray-700 mt-1.5 pl-3 border-l-2 border-emerald-700">
                                    <span class="font-semibold">Hasil:</span> {{ $followup->result }}
                                </p>
                            @endif
                            @if($followup->notes)
                                <p class="text-[11px] text-gray-500 mt-1 italic">{{ $followup->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Form Tambah Tindak Lanjut --}}
        <div class="bg-white border border-gray-200 p-6 space-y-4">
            <div class="border-b border-gray-200 pb-3">
                <h3 class="font-semibold text-gray-900 text-xs uppercase tracking-wider">+ Catatan Tindak Lanjut</h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Catat pelaksanaan kegiatan pendampingan</p>
            </div>

            @if($coaching->status->value === 'done')
                <div class="p-4 bg-gray-50 border border-gray-200 text-center text-xs text-gray-500">
                    Pembinaan sudah dinyatakan selesai.
                </div>
            @else
                <form action="{{ route('coaching.followup', $coaching) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                            Tanggal Kegiatan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="followup_date" value="{{ old('followup_date', date('Y-m-d')) }}"
                               required class="input-base font-mono">
                        @error('followup_date') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                            Kegiatan Dilakukan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="activity" rows="3" required
                                  placeholder="Deskripsi kegiatan tindak lanjut..."
                                  class="input-base resize-none">{{ old('activity') }}</textarea>
                        @error('activity') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                            Hasil / Perkembangan
                        </label>
                        <textarea name="result" rows="2"
                                  placeholder="Hasil perkembangan setelah kegiatan..."
                                  class="input-base resize-none">{{ old('result') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                            Catatan Tambahan
                        </label>
                        <textarea name="notes" rows="2"
                                  placeholder="Catatan tambahan (opsional)..."
                                  class="input-base resize-none">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center">
                        Simpan Catatan
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>
@endsection
