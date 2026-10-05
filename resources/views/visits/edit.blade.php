@extends('layouts.app')

@section('title', 'Edit Kunjungan')
@section('page-title', 'Edit Jadwal Kunjungan')

@section('content')
<div class="py-4 max-w-2xl">

    <div class="bg-white border border-gray-200 p-6">
        <div class="border-b border-gray-200 pb-4 mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Perbarui Jadwal Kunjungan</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ $visit->business->name }} &mdash; {{ $visit->business->member->full_name }}</p>
            </div>
            <a href="{{ route('visits.show', $visit) }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('visits.update', $visit) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Tanggal Kunjungan & Periode Evaluasi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="visit_date" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                        Tanggal Kunjungan <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="visit_date"
                           id="visit_date"
                           value="{{ old('visit_date', $visit->visit_date->format('Y-m-d')) }}"
                           required
                           class="input-base font-mono @error('visit_date') border-red-500 bg-red-50 @enderror">
                    @error('visit_date')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="evaluation_period" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                        Periode Evaluasi (YYYY-MM) <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="evaluation_period"
                           id="evaluation_period"
                           value="{{ old('evaluation_period', $visit->evaluation_period) }}"
                           pattern="\d{4}-\d{2}"
                           required
                           class="input-base font-mono @error('evaluation_period') border-red-500 bg-red-50 @enderror">
                    @error('evaluation_period')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Catatan Lapangan --}}
            <div>
                <label for="field_notes" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Agenda / Catatan Kunjungan
                </label>
                <textarea name="field_notes"
                          id="field_notes"
                          rows="4"
                          class="input-base resize-none @error('field_notes') border-red-500 bg-red-50 @enderror">{{ old('field_notes', $visit->field_notes) }}</textarea>
                @error('field_notes')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="pt-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('visits.show', $visit) }}" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
