<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_data', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50)->comment('business_type, obstacle_category, coaching_type');
            $table->string('code', 50)->index();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['category', 'code']);
        });

        // Seed initial standard master data
        $initialData = [
            // Jenis Usaha
            ['category' => 'business_type', 'code' => 'KELONTONG', 'name' => 'Perdagangan & Toko Sembako', 'description' => 'Warung, kios sembako, bahan pokok'],
            ['category' => 'business_type', 'code' => 'KULINER', 'name' => 'Kuliner & Makanan Minuman', 'description' => 'Warung makan, katering, aneka jajanan'],
            ['category' => 'business_type', 'code' => 'JASA_OTOMOTIF', 'name' => 'Jasa Otomotif & Bengkel', 'description' => 'Servis motor/mobil, cuci steam, suku cadang'],
            ['category' => 'business_type', 'code' => 'KONVEKSI', 'name' => 'Konveksi, Tekstil & Jahit', 'description' => 'Busana muslim, jahit pakaian, konveksi'],
            ['category' => 'business_type', 'code' => 'PERTANIAN_PERIKANAN', 'name' => 'Pertanian & Budidaya Perikanan', 'description' => 'Sayuran hidroponik, lele, unggas'],
            ['category' => 'business_type', 'code' => 'JASA_LAINNYA', 'name' => 'Jasa & Layanan Lainnya', 'description' => 'Laundry, pangkas rambut, fotokopi'],

            // Kategori Kendala
            ['category' => 'obstacle_category', 'code' => 'MODAL', 'name' => 'Keterbatasan Permodalan / Likuiditas', 'description' => 'Kekurangan modal kerja atau perputaran kas'],
            ['category' => 'obstacle_category', 'code' => 'BAHAN_BAKU', 'name' => 'Fluktuasi & Kelangkaan Bahan Baku', 'description' => 'Harga bahan baku melonjak atau pasokan langka'],
            ['category' => 'obstacle_category', 'code' => 'PEMASARAN', 'name' => 'Jangkauan Pemasaran & Penjualan', 'description' => 'Persaingan ketat, minim promosi online'],
            ['category' => 'obstacle_category', 'code' => 'PEMBUKUAN', 'name' => 'Pencatatan Keuangan Masih Manual', 'description' => 'Uang pribadi tercampur dengan uang usaha'],
            ['category' => 'obstacle_category', 'code' => 'TEMPAT', 'name' => 'Lokasi Usaha & Fasilitas', 'description' => 'Sewa tempat habis, keterbatasan ruang produksi'],

            // Jenis Pembinaan
            ['category' => 'coaching_type', 'code' => 'PLAFOND', 'name' => 'Peningkatan Plafond Pembiayaan', 'description' => 'Diusulkan penambahan modal pembiayaan syariah'],
            ['category' => 'coaching_type', 'code' => 'PEMBUKUAN_PENDAMPINGAN', 'name' => 'Pendampingan Pembukuan Sederhana', 'description' => 'Pelatihan buku kas harian & pemisahan dompet'],
            ['category' => 'coaching_type', 'code' => 'DIGITAL_MARKETING', 'name' => 'Pelatihan Pemasaran Digital & Mitra BMI', 'description' => 'Promosi via WhatsApp Bisnis dan marketplace'],
            ['category' => 'coaching_type', 'code' => 'PENGAWASAN_KHUSUS', 'name' => 'Pengawasan & Monitoring Intensif', 'description' => 'Kunjungan berkala 2 minggu sekali'],
        ];

        foreach ($initialData as $item) {
            \Illuminate\Support\Facades\DB::table('master_data')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('master_data');
    }
};
