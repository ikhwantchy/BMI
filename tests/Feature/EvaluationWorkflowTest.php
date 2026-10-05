<?php

namespace Tests\Feature;

use App\Enums\EvaluationStatus;
use App\Enums\RecommendationStatus;
use App\Enums\VisitStatus;
use App\Models\Business;
use App\Models\Evaluation;
use App\Models\EvaluationParameter;
use App\Models\Member;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\EvaluationParameterSeeder::class);
    }

    public function test_full_evaluation_lifecycle(): void
    {
        $officer = User::factory()->create([
            'role'   => 'petugas_lapangan',
            'status' => 'active',
        ]);

        $manager = User::factory()->create([
            'role'   => 'manajer',
            'status' => 'active',
        ]);

        $member = Member::create([
            'member_number'     => 'BMI-TEST-002',
            'full_name'         => 'Siti Rahma',
            'address'           => 'Jl. Anggrek No. 5',
            'membership_status' => 'active',
        ]);

        $business = Business::create([
            'member_id'     => $member->id,
            'name'          => 'Warung Rahma',
            'business_type' => 'Kuliner',
            'status'        => 'active',
        ]);

        // 1. Petugas membuat jadwal kunjungan
        $response = $this->actingAs($officer)->post(route('visits.store'), [
            'business_id'       => $business->id,
            'visit_date'        => now()->format('Y-m-d'),
            'evaluation_period' => '2026-10',
            'field_notes'       => 'Observasi awal',
        ]);
        $response->assertRedirect();

        $visit = Visit::first();
        $this->assertNotNull($visit);
        $this->assertEquals(VisitStatus::Scheduled, $visit->status);

        // 2. Petugas menyelesaikan kunjungan
        $response = $this->actingAs($officer)->post(route('visits.complete', $visit));
        $response->assertRedirect();
        $this->assertEquals(VisitStatus::Completed, $visit->fresh()->status);

        // 3. Petugas mengisi form evaluasi
        $parameters = EvaluationParameter::all();
        $scores = [];
        foreach ($parameters as $p) {
            $scores[$p->id] = 85;
        }

        $response = $this->actingAs($officer)->post(route('evaluations.store', $visit), [
            'scores' => $scores,
        ]);
        $response->assertRedirect();

        $evaluation = Evaluation::first();
        $this->assertNotNull($evaluation);
        $this->assertEquals(EvaluationStatus::Draft, $evaluation->status);
        $this->assertEquals(85.0, (float) $evaluation->total_score);
        $this->assertEquals(RecommendationStatus::Recommended, $evaluation->recommendation);

        // 4. Petugas mengajukan evaluasi (submit)
        $response = $this->actingAs($officer)->post(route('evaluations.submit', $evaluation));
        $response->assertRedirect();
        $this->assertEquals(EvaluationStatus::WaitingValidation, $evaluation->fresh()->status);

        // 5. Petugas tidak boleh memvalidasi sendiri
        $response = $this->actingAs($officer)->post(route('evaluations.validate', $evaluation), [
            'validator_notes' => 'Coba validasi sendiri',
        ]);
        $response->assertForbidden();

        // 6. Manajer menyetujui dan memvalidasi
        $response = $this->actingAs($manager)->post(route('evaluations.validate', $evaluation), [
            'validator_notes' => 'Disetujui untuk peningkatan pembiayaan',
        ]);
        $response->assertRedirect();

        $evaluation = $evaluation->fresh();
        $this->assertEquals(EvaluationStatus::Validated, $evaluation->status);
        $this->assertEquals($manager->id, $evaluation->validated_by);
        $this->assertEquals('Disetujui untuk peningkatan pembiayaan', $evaluation->validator_notes);
    }
}
