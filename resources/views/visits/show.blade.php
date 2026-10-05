@extends('layouts.app')

@section('title', 'Detail Kunjungan: ' . $visit->business->name)
@section('page-title', 'Detail Kunjungan Lapangan')

@section('content')

@if(auth()->user()->hasRole('petugas_lapangan') && in_array($visit->status->value, ['scheduled', 'in_progress']))
    @include('visits.execute-form')
@else

<div class="py-4 space-y-6">

    {{-- Breadcrumb & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 pb-4">
        <div class="flex items-center space-x-2 text-xs text-gray-500">
            <a href="{{ route('visits.index') }}" class="hover:text-emerald-700 transition">Kunjungan</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">{{ $visit->business->name }}</span>
            <span>/</span>
            <span class="text-gray-500">{{ $visit->visit_date->translatedFormat('d M Y') }}</span>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($visit->status->value === 'scheduled')
                <form action="{{ route('visits.complete', $visit) }}" method="POST" onsubmit="return confirm('Tandai kunjungan ini sebagai selesai?')">
                    @csrf
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                        Tandai Selesai
                    </button>
                </form>
                <a href="{{ route('visits.edit', $visit) }}" class="btn-secondary">
                    Edit
                </a>
            @endif

            @if($visit->status->value === 'completed')
                @if(!$visit->evaluation)
                    <a href="{{ route('evaluations.create', $visit) }}" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Input Form Evaluasi & Skor
                    </a>
                @else
                    <a href="{{ route('evaluations.show', $visit->evaluation) }}" class="btn-secondary">
                        Lihat Hasil Evaluasi &rarr;
                    </a>
                @endif
            @endif
        </div>
    </div>

    {{-- Visit Info Card --}}
    <div class="bg-white border border-gray-200 p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-200 gap-3">
            <div>
                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Unit Usaha Binaan</span>
                <h3 class="text-base font-semibold text-gray-900 mt-0.5">
                    <a href="{{ route('businesses.show', $visit->business) }}" class="hover:text-emerald-700">
                        {{ $visit->business->name }}
                    </a>
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Anggota: <span class="font-medium text-gray-800">{{ $visit->business->member->full_name }}</span> ({{ $visit->business->member->member_number }})
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="badge border bg-{{ $visit->status->badgeColor() }}-50 border-{{ $visit->status->badgeColor() }}-200 text-{{ $visit->status->badgeColor() }}-800">
                    {{ $visit->status->label() }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Kunjungan</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900 font-mono">{{ $visit->visit_date->translatedFormat('l, d F Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Periode Evaluasi</dt>
                <dd class="mt-1 text-sm font-semibold font-mono text-gray-900">{{ $visit->evaluation_period }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Petugas Pendamping</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $visit->officer?->name ?: '-' }}</dd>
            </div>
            <div class="md:col-span-3">
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Catatan Lapangan & Temuan</dt>
                <dd class="mt-1 text-sm text-gray-800 bg-gray-50 p-4 border border-gray-200 whitespace-pre-line leading-relaxed">
                    {{ $visit->field_notes ?: 'Tidak ada catatan khusus yang dicantumkan.' }}
                </dd>
            </div>
        </div>
    </div>

    {{-- Evaluasi Section if exists --}}
    @if($visit->evaluation)
        <div class="bg-gray-50 border border-emerald-300 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-800">Hasil Evaluasi Lapangan</span>
                <div class="flex items-center space-x-3 mt-1">
                    <span class="text-xl font-semibold font-mono text-emerald-950">Skor: {{ number_format($visit->evaluation->final_score, 1) }}</span>
                    @if($visit->evaluation->recommendation)
                        <span class="badge border bg-emerald-100 border-emerald-300 text-emerald-900">
                            {{ $visit->evaluation->recommendation->label() }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-600 mt-1">Status Validasi: <span class="font-semibold">{{ $visit->evaluation->status->label() }}</span></p>
            </div>
            <a href="{{ route('evaluations.show', $visit->evaluation) }}" class="btn-primary self-start sm:self-center">
                Buka Lembar Evaluasi &rarr;
            </a>
        </div>
    @endif

    {{-- Dokumentasi & Foto Lapangan --}}
    <div class="bg-white border border-gray-200 p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-200 gap-3">
            <div>
                <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Dokumentasi & Bukti Foto Lapangan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Unggah foto kondisi tempat usaha, display produk, atau berkas pendukung</p>
            </div>
            <span class="text-xs text-gray-400 font-mono">{{ $visit->documents->count() }} Berkas</span>
        </div>

        {{-- Form Upload (Kamera & Auto Timestamp) --}}
        <form action="{{ route('visits.documents.store', $visit) }}" method="POST" enctype="multipart/form-data"
              class="bg-gray-50 p-4 border border-gray-200 flex flex-col sm:flex-row items-center gap-3"
              id="cameraForm">
            @csrf
            <div class="flex-1 w-full relative">
                <input type="file" name="document" id="cameraInput" required accept="image/*" capture="environment"
                       class="w-full text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:border file:border-gray-300 file:text-xs file:font-semibold file:bg-white file:text-gray-700 hover:file:bg-gray-50">
                <div id="processingIndicator" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-xs text-emerald-600 font-semibold animate-pulse">
                    Menambahkan Timestamp...
                </div>
            </div>
            <div class="w-full sm:w-64">
                <input type="text" name="caption" placeholder="Keterangan foto (opsional)..."
                       class="input-base">
            </div>
            <button type="submit" id="btnUpload" class="btn-primary whitespace-nowrap">
                + Unggah
            </button>
        </form>

        @push('scripts')
        <script>
            document.getElementById('cameraInput').addEventListener('change', async function(e) {
                const file = e.target.files[0];
                if (!file || !file.type.startsWith('image/')) return;

                const indicator = document.getElementById('processingIndicator');
                const btnUpload = document.getElementById('btnUpload');
                
                indicator.innerText = "Mengambil Lokasi & Memproses...";
                indicator.classList.remove('hidden');
                btnUpload.disabled = true;
                btnUpload.classList.add('opacity-50', 'cursor-not-allowed');

                // Dapatkan GPS Location
                let locationText = "Lokasi: GPS tidak tersedia/izin ditolak";
                try {
                    const pos = await new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(resolve, reject, { 
                            timeout: 10000, 
                            enableHighAccuracy: true 
                        });
                    });
                    const lat = pos.coords.latitude.toFixed(6);
                    const lng = pos.coords.longitude.toFixed(6);
                    const acc = Math.round(pos.coords.accuracy);
                    locationText = `Lokasi: Lat ${lat}, Lng ${lng} (Akurasi: ${acc}m)`;
                    
                    // Coba dapatkan alamat (Opsional, dibatasi 3 detik agar tidak lemot)
                    try {
                        const controller = new AbortController();
                        const timeoutId = setTimeout(() => controller.abort(), 3000);
                        const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18`, { signal: controller.signal });
                        if (res.ok) {
                            const data = await res.json();
                            if (data.display_name) {
                                // Potong alamat kalau kepanjangan
                                let addr = data.display_name.length > 70 ? data.display_name.substring(0, 70) + '...' : data.display_name;
                                locationText = `Lokasi: ${lat}, ${lng} - ${addr}`;
                            }
                        }
                        clearTimeout(timeoutId);
                    } catch(e) {}
                } catch(e) {
                    console.warn("GPS Error:", e);
                }

                const img = new Image();
                img.src = URL.createObjectURL(file);
                
                await new Promise(r => img.onload = r);

                const maxDim = 1600;
                let width = img.width;
                let height = img.height;

                if (width > maxDim || height > maxDim) {
                    if (width > height) {
                        height = Math.round((height * maxDim) / width);
                        width = maxDim;
                    } else {
                        width = Math.round((width * maxDim) / height);
                        height = maxDim;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                
                // Draw original image resized
                ctx.drawImage(img, 0, 0, width, height);

                // Styling Text (Lebih kecil, tidak tebal, dan tidak bertumpuk)
                const fontSize = Math.max(14, Math.floor(width * 0.022)); // Font lebih kecil
                const padding = fontSize * 1.5;
                const lineHeight = fontSize * 1.6;
                const bgHeight = (lineHeight * 3) + padding; // Untuk 3 baris text

                // Add dark semi-transparent background at the bottom
                ctx.fillStyle = 'rgba(0, 0, 0, 0.65)';
                ctx.fillRect(0, canvas.height - bgHeight, canvas.width, bgHeight);

                // Format timestamp
                const now = new Date();
                const timestamp = now.toLocaleDateString('id-ID', {
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                });

                // Set Base Font
                ctx.font = `${fontSize}px sans-serif`; // Normal, tidak bold
                ctx.textBaseline = 'top';
                
                let currentY = canvas.height - bgHeight + (padding / 2);

                // Baris 1: Kunjungan (Warna Kuning)
                ctx.fillStyle = '#e4c85b';
                ctx.fillText(`Kunjungan: {{ $visit->business->name }}`, padding, currentY);
                currentY += lineHeight;

                // Baris 2: Waktu (Warna Putih)
                ctx.fillStyle = '#ffffff';
                ctx.fillText(`Waktu: ${timestamp}`, padding, currentY);
                currentY += lineHeight;

                // Baris 3: Lokasi (Warna Putih)
                ctx.fillText(locationText, padding, currentY);

                // Convert back to file and replace input
                canvas.toBlob(blob => {
                    const newFile = new File([blob], file.name, { type: 'image/jpeg' });
                    const dt = new DataTransfer();
                    dt.items.add(newFile);
                    e.target.files = dt.files;
                    
                    indicator.classList.add('hidden');
                    indicator.innerText = "Menambahkan Timestamp..."; // Reset
                    btnUpload.disabled = false;
                    btnUpload.classList.remove('opacity-50', 'cursor-not-allowed');
                }, 'image/jpeg', 0.85);
            });
        </script>
        @endpush

        {{-- Gallery Grid --}}
        @if($visit->documents->isEmpty())
            <p class="text-xs text-gray-400 py-6 text-center italic">Belum ada foto atau berkas yang diunggah untuk kunjungan ini.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach($visit->documents as $doc)
                    <div class="border border-gray-200 bg-white flex flex-col">
                        @if($doc->isImage())
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="block h-36 overflow-hidden bg-gray-100">
                                <img src="{{ asset('storage/' . $doc->file_path) }}" alt="{{ $doc->caption ?: $doc->file_name }}"
                                     class="w-full h-full object-cover">
                            </a>
                        @else
                            <div class="h-36 flex items-center justify-center bg-gray-100 text-gray-500">
                                <span class="font-semibold text-xs font-mono">PDF / BERKAS</span>
                            </div>
                        @endif

                        <div class="p-2.5 flex-1 flex flex-col justify-between">
                            <p class="text-[11px] font-medium text-gray-800 truncate" title="{{ $doc->caption ?: $doc->file_name }}">
                                {{ $doc->caption ?: $doc->file_name }}
                            </p>
                            <div class="flex items-center justify-between mt-2 pt-1 border-t border-gray-100 text-[10px] text-gray-400">
                                <span class="font-mono">{{ $doc->fileSizeLabel() }}</span>
                                <form action="{{ route('visits.documents.destroy', [$visit, $doc]) }}" method="POST"
                                      onsubmit="return confirm('Hapus berkas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline font-semibold">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endif
@endsection
