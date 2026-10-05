<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\CoachingRecommendation;
use App\Models\Evaluation;
use App\Models\Member;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemNavigationTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;
    private User $officer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->manager = User::where('role', 'manajer')->first();
        $this->officer = User::where('role', 'petugas_lapangan')->first();
    }

    public function test_manager_can_access_dashboard_and_reports(): void
    {
        $response = $this->actingAs($this->manager)->get(route('dashboard'));
        $response->assertRedirect(route('members.index'));

        $response = $this->actingAs($this->manager)->get(route('reports.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->manager)->get(route('reports.evaluations'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->manager)->get(route('reports.recommendations'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->manager)->get(route('reports.export'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_officer_can_access_visits_and_businesses(): void
    {
        $response = $this->actingAs($this->officer)->get(route('dashboard'));
        $response->assertRedirect(route('members.index'));

        $response = $this->actingAs($this->officer)->get(route('businesses.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->officer)->get(route('visits.index'));
        $response->assertStatus(200);

        $business = Business::first();
        $response = $this->actingAs($this->officer)->get(route('businesses.show', $business));
        $response->assertStatus(200);

        $visit = Visit::first();
        $response = $this->actingAs($this->officer)->get(route('visits.show', $visit));
        $response->assertStatus(200);
    }

    public function test_coaching_recommendations_page_accessible(): void
    {
        $response = $this->actingAs($this->manager)->get(route('coaching.index'));
        $response->assertStatus(200);
    }
}
