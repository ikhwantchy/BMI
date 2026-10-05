<?php

namespace Tests\Feature;

use App\Enums\EvaluationStatus;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Evaluation;
use App\Models\Member;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditAndIntegrityTest extends TestCase
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

    public function test_login_and_logout_creates_audit_log(): void
    {
        // Failed login
        $this->post('/login', [
            'username' => 'nonexistent',
            'password' => 'wrongpass',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'failed_login',
        ]);

        // Successful login
        $this->post('/login', [
            'username' => 'admin',
            'password' => '@Bukan123',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action'  => 'login',
            'user_id' => $this->manager->id,
        ]);

        // Logout
        $this->actingAs($this->manager)->post('/logout');

        $this->assertDatabaseHas('audit_logs', [
            'action'  => 'logout',
            'user_id' => $this->manager->id,
        ]);
    }

    public function test_evaluations_can_be_soft_deleted(): void
    {
        $eval = Evaluation::first();
        $this->assertNotNull($eval);

        $eval->delete();

        $this->assertSoftDeleted('evaluations', ['id' => $eval->id]);
    }

    public function test_audit_log_page_accessible_by_manager_but_forbidden_to_officer(): void
    {
        // Officer forbidden
        $response = $this->actingAs($this->officer)->get(route('audit.index'));
        $response->assertForbidden();

        // Manager allowed
        $response = $this->actingAs($this->manager)->get(route('audit.index'));
        $response->assertOk();
        $response->assertSee('Jejak Audit');

        // CSV export
        $response = $this->actingAs($this->manager)->get(route('audit.export'));
        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
