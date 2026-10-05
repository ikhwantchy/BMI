<?php

namespace Tests\Unit;

use App\Enums\RecommendationStatus;
use App\Models\Business;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\EvaluationParameter;
use App\Models\Member;
use App\Models\User;
use App\Models\Visit;
use App\Services\ScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoringServiceTest extends TestCase
{
    use RefreshDatabase;

    private ScoringService $scoringService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scoringService = new ScoringService();
    }

    public function test_recommendation_mapping_thresholds(): void
    {
        // Case A: >= 80 -> Recommended
        $this->assertEquals(RecommendationStatus::Recommended, $this->scoringService->determineRecommendation(85.5));
        $this->assertEquals(RecommendationStatus::Recommended, $this->scoringService->determineRecommendation(80.0));

        // Case B: 60 - 79.9 -> ContinuedCoaching
        $this->assertEquals(RecommendationStatus::ContinuedCoaching, $this->scoringService->determineRecommendation(79.9));
        $this->assertEquals(RecommendationStatus::ContinuedCoaching, $this->scoringService->determineRecommendation(60.0));

        // Case C: < 60 -> NotRecommended
        $this->assertEquals(RecommendationStatus::NotRecommended, $this->scoringService->determineRecommendation(59.9));
        $this->assertEquals(RecommendationStatus::NotRecommended, $this->scoringService->determineRecommendation(40.0));
    }

    public function test_calculate_and_apply_score(): void
    {
        $this->seed(\Database\Seeders\EvaluationParameterSeeder::class);

        $user = User::factory()->create(['role' => 'petugas_lapangan']);
        $member = Member::create([
            'member_number'     => 'BMI-TEST-001',
            'full_name'         => 'Test Member',
            'address'           => 'Test Address',
            'membership_status' => 'active',
        ]);
        $business = Business::create([
            'member_id'     => $member->id,
            'name'          => 'Toko Test',
            'business_type' => 'Retail',
        ]);
        $visit = Visit::create([
            'business_id'       => $business->id,
            'officer_id'        => $user->id,
            'visit_date'        => now(),
            'evaluation_period' => '2026-10',
            'status'            => 'completed',
        ]);

        $evaluation = Evaluation::create([
            'visit_id'    => $visit->id,
            'business_id' => $business->id,
            'status'      => 'draft',
        ]);

        $parameters = EvaluationParameter::all();
        // Set all parameters to 90
        foreach ($parameters as $param) {
            EvaluationDetail::create([
                'evaluation_id' => $evaluation->id,
                'parameter_id'  => $param->id,
                'score'         => 90,
            ]);
        }

        $result = $this->scoringService->applyScore($evaluation);

        $this->assertEquals(90.0, (float) $result->total_score);
        $this->assertEquals(RecommendationStatus::Recommended, $result->recommendation);
        $this->assertStringContainsString('kinerja baik', $result->recommendation_reason);
    }
}
