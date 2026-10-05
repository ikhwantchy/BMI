<?php

/**
 * Konfigurasi Scoring Evaluasi Usaha Anggota
 * Koperasi Syariah Benteng Mikro Indonesia
 *
 * Semua threshold dan bobot dapat diubah di sini tanpa edit kode aplikasi.
 * Untuk perubahan yang lebih fleksibel, parameter dan bobotnya tersimpan di database.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Threshold Rekomendasi
    |--------------------------------------------------------------------------
    | Berdasarkan dokumen spesifikasi yang telah divalidasi.
    | score >= recommended          => Direkomendasikan
    | score >= continued_coaching   => Pembinaan Lanjutan
    | score < continued_coaching    => Tidak Direkomendasikan
    */
    'recommendation_thresholds' => [
        'recommended'        => 80,
        'continued_coaching' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Parameter Evaluasi (Seed Data)
    |--------------------------------------------------------------------------
    | Bobot yang telah dikonfirmasi. Total harus = 1.0000
    | OPEN ITEM: indikator rinci per parameter belum final.
    */
    'parameters' => [
        [
            'code'       => 'kondisi_usaha',
            'name'       => 'Kondisi Usaha',
            'weight'     => 0.20,
            'sort_order' => 1,
        ],
        [
            'code'       => 'perkembangan_omzet',
            'name'       => 'Perkembangan Omzet',
            'weight'     => 0.25,
            'sort_order' => 2,
        ],
        [
            'code'       => 'aktivitas_usaha',
            'name'       => 'Aktivitas Usaha',
            'weight'     => 0.20,
            'sort_order' => 3,
        ],
        [
            'code'       => 'pengelolaan_keuangan',
            'name'       => 'Pengelolaan Keuangan',
            'weight'     => 0.20,
            'sort_order' => 4,
        ],
        [
            'code'       => 'kendala_usaha',
            'name'       => 'Kendala Usaha',
            'weight'     => 0.15,
            'sort_order' => 5,
        ],
    ],

];
