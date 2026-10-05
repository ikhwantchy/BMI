<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_member_list(): void
    {
        $user = User::factory()->create(['role' => 'petugas_lapangan']);
        Member::create([
            'member_number'     => 'BMI-001',
            'full_name'         => 'Ahmad Test',
            'address'           => 'Jl. Test No. 1',
            'membership_status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('members.index'));
        $response->assertOk();
        $response->assertSee('Ahmad Test');
    }

    public function test_user_can_create_member(): void
    {
        $user = User::factory()->create(['role' => 'petugas_lapangan']);

        $response = $this->actingAs($user)->post(route('members.store'), [
            'member_number'     => 'BMI-NEW-01',
            'full_name'         => 'Budi Baru',
            'address'           => 'Jl. Baru No. 10',
            'phone'             => '08123456789',
            'membership_status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'member_number' => 'BMI-NEW-01',
            'full_name'     => 'Budi Baru',
        ]);
    }

    public function test_officer_cannot_delete_member_but_manager_can(): void
    {
        $officer = User::factory()->create(['role' => 'petugas_lapangan']);
        $manager = User::factory()->create(['role' => 'manajer']);

        $member = Member::create([
            'member_number'     => 'BMI-DEL-01',
            'full_name'         => 'Member Hapus',
            'address'           => 'Alamat Hapus',
            'membership_status' => 'active',
        ]);

        // Petugas dilarang menghapus
        $response = $this->actingAs($officer)->delete(route('members.destroy', $member));
        $response->assertForbidden();

        // Manajer diizinkan menghapus
        $response = $this->actingAs($manager)->delete(route('members.destroy', $member));
        $response->assertRedirect(route('members.index'));
        $this->assertSoftDeleted('members', ['id' => $member->id]);
    }
}
