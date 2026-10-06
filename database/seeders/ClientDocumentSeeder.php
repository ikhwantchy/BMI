<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\DocumentHistory;
use App\Models\FeasibilityAssessment;
use App\Models\FinancingAnalysis;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $officer = User::where('role', 'petugas_lapangan')->first() ?? User::first();
        $manager = User::where('role', 'manajer')->first() ?? User::first();

        // 1. Update Existing Members with client demographic data
        $siti = Member::where('member_number', 'BMI-2024-001')->first();
        if ($siti) {
            $siti->update([
                'nik'               => '3216015504820001',
                'birth_place_date'  => 'Tangerang, 15 April 1982',
                'marital_status'    => 'Menikah',
                'education'         => 'SMA / Sederajat',
                'rembug_pusat'      => 'RP Berkah Mandiri 01',
                'registration_year' => 2023,
                'spouse_name'       => 'H. Usman Supardi',
                'spouse_nik'        => '3216011208790002',
                'spouse_phone'      => '081298877665',
                'spouse_occupation' => 'Pedagang Grosir Beras',
                'spouse_income'     => 4500000,
                'dependents_count'  => 3,
            ]);
        }

        $bambang = Member::where('member_number', 'BMI-2024-004')->first();
        if ($bambang) {
            $bambang->update([
                'nik'               => '3216082001850003',
                'birth_place_date'  => 'Balaraja, 20 Januari 1985',
                'marital_status'    => 'Menikah',
                'education'         => 'SMK',
                'rembug_pusat'      => 'RP Mina Makmur Balaraja',
                'registration_year' => 2024,
                'spouse_name'       => 'Siti Rohani',
                'spouse_nik'        => '3216086509870004',
                'spouse_phone'      => '087822334455',
                'spouse_occupation' => 'Ibu Rumah Tangga / Bantu Usaha',
                'spouse_income'     => 1500000,
                'dependents_count'  => 2,
            ]);
        }

        // 2. Update Existing Businesses with financial figures
        $b1 = Business::where('name', 'like', '%Sembako Berkah Siti%')->first();
        if ($b1) {
            $b1->update([
                'monthly_turnover'   => 45000000,
                'daily_turnover'     => 1500000,
                'net_monthly_income' => 7500000,
                'workforce_count'    => 2,
                'start_year'         => 2021,
            ]);
        }

        $b4 = Business::where('name', 'like', '%Budidaya Lele%')->first();
        if ($b4) {
            $b4->update([
                'monthly_turnover'   => 28000000,
                'daily_turnover'     => 950000,
                'net_monthly_income' => 5800000,
                'workforce_count'    => 1,
                'start_year'         => 2022,
            ]);
        }

        // 3. Create Uji Kelayakan for Hj. Siti Aminah
        if ($siti) {
            $fa1 = FeasibilityAssessment::updateOrCreate(
                ['assessment_number' => 'UK-2024-001'],
                [
                    'member_id'                => $siti->id,
                    'branch_id'                => $siti->branch_id,
                    'user_id'                  => $officer->id,
                    'assessment_date'          => '2024-01-10',
                    'nik'                      => $siti->nik,
                    'birth_place_date'         => $siti->birth_place_date,
                    'marital_status'           => $siti->marital_status,
                    'education'                => $siti->education,
                    'rt_rw'                    => '004 / 002',
                    'village'                  => 'Pasir Kaliki',
                    'district'                 => 'Curug',
                    'spouse_name'              => $siti->spouse_name,
                    'spouse_nik'               => $siti->spouse_nik,
                    'spouse_occupation'        => $siti->spouse_occupation,
                    'spouse_income'            => $siti->spouse_income,
                    'spouse_phone'             => $siti->spouse_phone,
                    'dependents_count'         => $siti->dependents_count,
                    'schooling_children_count' => 2,
                    'home_ownership_status'    => 'Milik Sendiri (SHM)',
                    'wall_type'                => 'Tembok Permanen Diplester dan Dicat',
                    'floor_type'               => 'Keramik Bersih 40x40',
                    'roof_type'                => 'Genteng Tanah Liat',
                    'water_source'             => 'Sumur Bor / Pompa Listrik Jernih',
                    'electricity_power'        => 'PLN Pascabayar 1300 VA',
                    'land_home_assets'         => 'Rumah tinggal 72 m2 & tanah 110 m2 milik pribadi',
                    'vehicle_assets'           => '2 Unit Sepeda Motor (Honda Beat 2021 & Vario 2023)',
                    'electronic_assets'        => 'Kulkas 2 pintu, TV LED 43 inch, Mesin cuci automatic',
                    'savings_gold_assets'      => 'Tabungan Bank Syariah & Perhiasan Emas ±15 gram',
                    'community_relation'       => 'Sangat Baik dan Dihormati Tetangga',
                    'rembug_pusat_activity'    => 'Aktif dan Disiplin Mengikuti Pertemuan Mingguan',
                    'reputation_character'     => 'Amanah, jujur, tertib membayar kewajiban, tidak memiliki catatan buruk',
                    'result'                   => 'memenuhi_syarat',
                    'notes'                    => 'Kondisi sosial, keluarga, dan lingkungan sangat layak untuk diberikan keanggotaan dan pembiayaan.',
                    'status'                   => 'validated',
                    'validated_by'             => $manager->id,
                    'validated_at'             => '2024-01-12 10:00:00',
                    'validator_notes'          => 'Disetujui. Hasil verifikasi lapangan valid dan memenuhi kriteria syariah BMI.',
                ]
            );

            // Document History for Uji Kelayakan
            DocumentHistory::updateOrCreate(
                ['document_number' => 'DOC-UK-2024-001'],
                [
                    'document_type' => 'feasibility_assessment',
                    'reference_id'  => $fa1->id,
                    'member_id'     => $siti->id,
                    'business_id'   => $b1?->id,
                    'generated_by'  => $officer->id,
                    'snapshot_data' => $fa1->toArray(),
                ]
            );
        }

        // 4. Create Analisis Pembiayaan for Hj. Siti Aminah
        if ($siti && $b1) {
            $ana1 = FinancingAnalysis::updateOrCreate(
                ['analysis_number' => 'AP-2024-001'],
                [
                    'member_id'                    => $siti->id,
                    'business_id'                  => $b1->id,
                    'branch_id'                    => $siti->branch_id,
                    'user_id'                      => $officer->id,
                    'analysis_date'                => '2024-01-15',
                    'spouse_name'                  => $siti->spouse_name,
                    'rembug_pusat'                 => $siti->rembug_pusat,
                    'registration_year'            => $siti->registration_year,
                    'proposed_amount'              => 20000000,
                    'financing_purpose'            => 'Penambahan stok persediaan sembako menghadapi momentum bulan Ramadhan dan Hari Raya',
                    'last_financing_amount'        => 10000000,
                    'approved_ceiling'             => 20000000,
                    'investment_ceiling'           => 3000000,
                    'financing_other_institution'  => 0,
                    'business_type'                => $b1->business_type,
                    'business_start_year'          => $b1->start_year,
                    'monthly_turnover'             => $b1->monthly_turnover,
                    'daily_turnover'               => $b1->daily_turnover,
                    'workforce_count'              => $b1->workforce_count,
                    'net_business_income'          => $b1->net_monthly_income,
                    'business_assets_estimate'     => 35000000,
                    'savings_amount'               => 5500000,
                    'electronic_assets_estimate'   => 8000000,
                    'vehicle_assets_estimate'      => 25000000,
                    'total_assets_estimate'        => 73500000,
                    'business_income'              => 7500000,
                    'spouse_income'                => 4500000,
                    'total_income'                 => 12000000,
                    'per_capita_income'            => 3000000, // (12jt / 4 jiwa)
                    'household_expenses'           => 4500000,
                    'business_expenses'            => 1500000,
                    'other_installments'           => 0,
                    'total_expenses'               => 6000000,
                    'saving_capacity'              => 6000000, // (12jt - 6jt)
                    'installment_capacity'         => 4500000, // (75% dari 6jt)
                    'conclusion'                   => 'layak',
                    'notes'                        => 'Arus kas dan kapasitas menabung sangat mencukupi untuk angsuran mingguan/bulanan. Usaha stabil.',
                    'status'                       => 'validated',
                    'validated_by'                 => $manager->id,
                    'validated_at'                 => '2024-01-16 14:30:00',
                    'validator_notes'              => 'Disetujui plafon penuh Rp 20.000.000 dengan jangka waktu 50 minggu.',
                ]
            );

            // Document History for Analisis Pembiayaan
            DocumentHistory::updateOrCreate(
                ['document_number' => 'DOC-AP-2024-001'],
                [
                    'document_type' => 'financing_analysis',
                    'reference_id'  => $ana1->id,
                    'member_id'     => $siti->id,
                    'business_id'   => $b1->id,
                    'generated_by'  => $officer->id,
                    'snapshot_data' => $ana1->toArray(),
                ]
            );
        }
    }
}
