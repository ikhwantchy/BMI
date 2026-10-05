<?php

namespace Database\Seeders;

use App\Enums\EvaluationStatus;
use App\Enums\MembershipStatus;
use App\Enums\RecommendationStatus;
use App\Enums\VisitStatus;
use App\Models\Business;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\EvaluationParameter;
use App\Models\Member;
use App\Models\User;
use App\Models\Visit;
use App\Services\ScoringService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $officer = User::where('role', 'petugas_lapangan')->first();
        $manager = User::where('role', 'manajer')->first();
        $branch  = \App\Models\Branch::first();
        $branchId = $branch?->id ?? 1;

        // 1. Members
        $membersData = [
            [
                'member_number'     => 'BMI-2024-001',
                'full_name'         => 'Hj. Siti Aminah',
                'phone'             => '081234567890',
                'address'           => 'Jl. Raya Pasir Kaliki No. 45, Tangerang',
                'notes'             => 'Anggota sejak Januari 2023, usaha sembako lancar.',
                'membership_status' => MembershipStatus::Active,
                'branch_id'         => $branchId,
            ],
            [
                'member_number'     => 'BMI-2024-002',
                'full_name'         => 'Ahmad Fauzi',
                'phone'             => '081398765432',
                'address'           => 'Kp. Sukamaju RT 03/RW 02, Curug, Tangerang',
                'notes'             => 'Anggota aktif, pengajuan pembiayaan untuk bengkel motor.',
                'membership_status' => MembershipStatus::Active,
                'branch_id'         => $branchId,
            ],
            [
                'member_number'     => 'BMI-2024-003',
                'full_name'         => 'Nurul Hidayati',
                'phone'             => '085712345678',
                'address'           => 'Perum Griya Asri Blok B2 No. 12, Cikupa',
                'notes'             => 'Pengrajin konveksi busana muslimah.',
                'membership_status' => MembershipStatus::Active,
                'branch_id'         => $branchId,
            ],
            [
                'member_number'     => 'BMI-2024-004',
                'full_name'         => 'Bambang Supriyanto',
                'phone'             => '087811223344',
                'address'           => 'Jl. Merdeka Barat No. 88, Balaraja',
                'notes'             => 'Peternak budidaya lele.',
                'membership_status' => MembershipStatus::Active,
                'branch_id'         => $branchId,
            ],
        ];

        $members = [];
        foreach ($membersData as $mData) {
            $members[] = Member::firstOrCreate(
                ['member_number' => $mData['member_number']],
                $mData
            );
        }

        // 2. Businesses
        $b1 = Business::firstOrCreate(
            ['member_id' => $members[0]->id, 'name' => 'Toko Sembako Berkah Siti'],
            [
                'business_type'       => 'Perdagangan / Kelontong',
                'address'             => 'Pasar Curug Kios No. 14, Tangerang',
                'business_age_months' => 36,
                'initial_capital'     => 15000000,
                'products_services'   => 'Beras, minyak goreng, gula, sembako, dan kebutuhan pokok harian.',
                'initial_condition'   => 'Usaha sudah berjalan stabil dengan omset harian rutin Rp 2-3 juta.',
                'status'              => 'active',
                'branch_id'           => $branchId,
            ]
        );

        $b2 = Business::firstOrCreate(
            ['member_id' => $members[1]->id, 'name' => 'Bengkel Motor Fauzi Mandiri'],
            [
                'business_type'       => 'Jasa Otomotif',
                'address'             => 'Jl. Raya Pemda No. 10, Curug',
                'business_age_months' => 18,
                'initial_capital'     => 10000000,
                'products_services'   => 'Servis motor, ganti oli, dan penjualan suku cadang motor.',
                'initial_condition'   => 'Pelanggan ramai, memerlukan modal tambahan untuk etalase suku cadang resmi.',
                'status'              => 'active',
                'branch_id'           => $branchId,
            ]
        );

        $b3 = Business::firstOrCreate(
            ['member_id' => $members[2]->id, 'name' => 'Konveksi Hijab Zahra'],
            [
                'business_type'       => 'Industri Rumahan / Tekstil',
                'address'             => 'Perum Griya Asri Blok B2 No. 12',
                'business_age_months' => 24,
                'initial_capital'     => 8000000,
                'products_services'   => 'Jahit jilbab, gamis syari, mukena pesanan reseller.',
                'initial_condition'   => 'Memiliki 3 mesin jahit, pasokan kain terkadang tersendat.',
                'status'              => 'active',
                'branch_id'           => $branchId,
            ]
        );

        $b4 = Business::firstOrCreate(
            ['member_id' => $members[3]->id, 'name' => 'Budidaya Lele Sangkuriang'],
            [
                'business_type'       => 'Perikanan / Pertanian',
                'address'             => 'Kp. Dukuh RT 01/RW 04, Balaraja',
                'business_age_months' => 8,
                'initial_capital'     => 5000000,
                'products_services'   => 'Bibit lele dan lele siap konsumsi ke warung pecel lele.',
                'initial_condition'   => '4 kolam terpal, sering terkendala harga pakan pelet fluktuatif.',
                'status'              => 'active',
                'branch_id'           => $branchId,
            ]
        );

        // 3. Visits
        $v1 = Visit::create([
            'business_id'       => $b1->id,
            'officer_id'        => $officer->id,
            'visit_date'        => now()->subDays(15),
            'evaluation_period' => now()->format('Y-m'),
            'status'            => VisitStatus::Completed,
            'field_notes'       => 'Pemeriksaan stok barang sembako, pencatatan kas masuk buku harian tertib.',
            'branch_id'         => $branchId,
        ]);

        $v2 = Visit::create([
            'business_id'       => $b2->id,
            'officer_id'        => $officer->id,
            'visit_date'        => now()->subDays(8),
            'evaluation_period' => now()->format('Y-m'),
            'status'            => VisitStatus::Completed,
            'field_notes'       => 'Kunjungan rutin, pelanggan servis harian mencapai 8-10 motor. Butuh pembinaan pencatatan stok oli.',
            'branch_id'         => $branchId,
        ]);

        $v3 = Visit::create([
            'business_id'       => $b3->id,
            'officer_id'        => $officer->id,
            'visit_date'        => now()->subDays(3),
            'evaluation_period' => now()->format('Y-m'),
            'status'            => VisitStatus::Completed,
            'field_notes'       => 'Pemeriksaan pesanan jilbab jelang musim liburan.',
            'branch_id'         => $branchId,
        ]);

        $v4 = Visit::create([
            'business_id'       => $b4->id,
            'officer_id'        => $officer->id,
            'visit_date'        => now()->addDays(2),
            'evaluation_period' => now()->format('Y-m'),
            'status'            => VisitStatus::Scheduled,
            'field_notes'       => 'Jadwal monitoring masa panen siklus ke-3 kolam lele.',
            'branch_id'         => $branchId,
        ]);

        // 4. Evaluations & Details
        $parameters = EvaluationParameter::all();
        $scoringService = app(ScoringService::class);

        // Evaluation 1 (Validated, High Score >= 80 -> Lanjutkan & Tingkatkan)
        $eval1 = Evaluation::create([
            'visit_id'         => $v1->id,
            'business_id'      => $b1->id,
            'status'           => EvaluationStatus::Validated,
            'submitted_by'     => $officer->id,
            'submitted_at'     => now()->subDays(14),
            'validated_by'     => $manager->id,
            'validated_at'     => now()->subDays(13),
            'validator_notes'  => 'Hasil evaluasi sangat baik, pembukuan rapih, disetujui untuk peningkatan plafond pembiayaan.',
            'branch_id'        => $branchId,
        ]);
        foreach ($parameters as $p) {
            EvaluationDetail::create([
                'evaluation_id' => $eval1->id,
                'parameter_id'  => $p->id,
                'score'         => 88,
                'notes'         => 'Kinerja sangat memuaskan dan disiplin syariah terjaga.',
            ]);
        }
        $scoringService->applyScore($eval1);

        // Evaluation 2 (Waiting Validation, Moderate Score 60-79 -> Pembinaan Khusus)
        $eval2 = Evaluation::create([
            'visit_id'         => $v2->id,
            'business_id'      => $b2->id,
            'status'           => EvaluationStatus::WaitingValidation,
            'submitted_by'     => $officer->id,
            'submitted_at'     => now()->subDays(7),
            'branch_id'        => $branchId,
        ]);
        foreach ($parameters as $p) {
            EvaluationDetail::create([
                'evaluation_id' => $eval2->id,
                'parameter_id'  => $p->id,
                'score'         => 72,
                'notes'         => 'Omset cukup baik namun pencatatan keuangan manual masih perlu pendampingan.',
            ]);
        }
        $scoringService->applyScore($eval2);

        // Evaluation 3 (Draft, to be edited or submitted by officer)
        $eval3 = Evaluation::create([
            'visit_id'         => $v3->id,
            'business_id'      => $b3->id,
            'status'           => EvaluationStatus::Draft,
            'branch_id'        => $branchId,
        ]);
        foreach ($parameters as $p) {
            EvaluationDetail::create([
                'evaluation_id' => $eval3->id,
                'parameter_id'  => $p->id,
                'score'         => 65,
                'notes'         => 'Stok kain masih terbatas.',
            ]);
        }
        $scoringService->applyScore($eval3);

        $this->command->info('✅ Demo data lengkap berhasil dibuat: 4 Anggota, 4 Usaha, 4 Kunjungan, 3 Evaluasi');
    }
}
