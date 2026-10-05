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

