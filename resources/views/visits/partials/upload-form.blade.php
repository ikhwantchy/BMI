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
            <p id="noDocumentText" class="text-xs text-gray-400 py-6 text-center italic">Belum ada foto atau berkas yang diunggah untuk kunjungan ini.</p>
        @else
            <div id="documentGallery" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
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
                                <form action="{{ route('visits.documents.destroy', [$visit, $doc]) }}" method="POST" id="form-doc-{{ $doc->id }}"
                                      onsubmit="return confirm('Hapus berkas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline font-semibold">Hapus</button>
                                </form>
                            </div>
<script>
document.getElementById('cameraForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnUpload');
    btn.disabled = true;
    btn.innerText = "Mengunggah...";
    
    try {
        const formData = new FormData(this);
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if(response.ok) {
            const data = await response.json();
            if(data.success) {
                let gallery = document.getElementById('documentGallery');
                const noData = document.getElementById('noDocumentText');
                
                if(!gallery) {
                    gallery = document.createElement('div');
                    gallery.id = 'documentGallery';
                    gallery.className = 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4';
                    if(noData) noData.replaceWith(gallery);
                } else if(noData) {
                    noData.remove();
                }
                
                const html = `
                    <div class="border border-gray-200 bg-white flex flex-col" id="doc-${data.document.id}">
                        ${data.document.is_image ? `
                            <a href="${data.document.file_path}" target="_blank" class="block h-36 overflow-hidden bg-gray-100">
                                <img src="${data.document.file_path}" alt="${data.document.caption}" class="w-full h-full object-cover">
                            </a>
                        ` : `
                            <div class="h-36 flex items-center justify-center bg-gray-100 text-gray-500">
                                <span class="font-semibold text-xs font-mono">PDF / BERKAS</span>
                            </div>
                        `}
                        <div class="p-2.5 flex-1 flex flex-col justify-between">
                            <p class="text-[11px] font-medium text-gray-800 truncate" title="${data.document.caption}">
                                ${data.document.caption}
                            </p>
                            <div class="flex items-center justify-between mt-2 pt-1 border-t border-gray-100 text-[10px] text-gray-400">
                                <span class="font-mono">${data.document.size}</span>
                                <button type="button" onclick="deleteDocument('${data.document.delete_url}', 'doc-${data.document.id}')" class="text-red-600 hover:underline font-semibold">Hapus</button>
                            </div>
                        </div>
                    </div>
                `;
                gallery.insertAdjacentHTML('beforeend', html);
                this.reset();
            }
        } else {
            alert("Terjadi kesalahan saat mengunggah foto.");
        }
    } catch(e) {
        console.error(e);
        alert("Gagal menghubungi server.");
    } finally {
        btn.disabled = false;
        btn.innerText = "+ Unggah";
    }
});

window.deleteDocument = async function(url, elId) {
    if(!confirm('Hapus berkas ini?')) return;
    try {
        const res = await fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        if(res.ok) {
            document.getElementById(elId).remove();
        }
    } catch(e) {
        console.error(e);
    }
}
</script>
