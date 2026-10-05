<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\MasterData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MasterAndImportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $officer;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'code' => 'KPS-TEST',
            'name' => 'Cabang Test',
            'city' => 'Tangerang',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'role'      => 'system_admin',
            'branch_id' => $this->branch->id,
            'status'    => 'active',
        ]);

        $this->officer = User::factory()->create([
            'role'      => 'petugas_lapangan',
            'branch_id' => $this->branch->id,
            'status'    => 'active',
        ]);
    }

    public function test_admin_can_access_and_create_master_data(): void
    {
        $response = $this->actingAs($this->admin)->get(route('master.index'));
        $response->assertStatus(200);

        $createResponse = $this->actingAs($this->admin)->post(route('master.store'), [
            'category'    => 'business_type',
            'code'        => 'KULINER_TEST',
            'name'        => 'Kuliner dan Katering',
            'description' => 'Usaha makanan olahan',
        ]);

        $createResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('master_data', [
            'code' => 'KULINER_TEST',
            'name' => 'Kuliner dan Katering',
        ]);
    }

    public function test_officer_cannot_manage_master_data(): void
    {
        $response = $this->actingAs($this->officer)->get(route('master.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_import_page_and_download_template(): void
    {
        $response = $this->actingAs($this->admin)->get(route('import.index'));
        $response->assertStatus(200);

        $downloadMembers = $this->actingAs($this->admin)->get(route('import.template', 'members'));
        $downloadMembers->assertStatus(200);
        $downloadMembers->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $downloadBusinesses = $this->actingAs($this->admin)->get(route('import.template', 'businesses'));
        $downloadBusinesses->assertStatus(200);
    }

    public function test_admin_can_preview_and_confirm_csv_import(): void
    {
        $csvContent = "nomor_anggota,nama_lengkap,alamat,telepon,status_keanggotaan,catatan\n" .
                      "BMI-CSV-001,Anggota CSV Satu,Jl. Mawar No. 1,0811111111,active,Catatan satu\n" .
                      "BMI-CSV-002,Anggota CSV Dua,Jl. Melati No. 2,0822222222,active,Catatan dua\n";

        $file = UploadedFile::fake()->createWithContent('members.csv', $csvContent);

        $previewResponse = $this->actingAs($this->admin)->post(route('import.preview'), [
            'type' => 'members',
            'file' => $file,
        ]);

        $previewResponse->assertStatus(200);
        $previewResponse->assertSee('Anggota CSV Satu');
        $previewResponse->assertSee('Anggota CSV Dua');

        $importToken = $previewResponse->viewData('importToken');
        $this->assertNotEmpty($importToken);

        $confirmResponse = $this->actingAs($this->admin)->post(route('import.confirm'), [
            'import_token' => $importToken,
        ]);

        $confirmResponse->assertRedirect(route('members.index'));
        $this->assertDatabaseHas('members', ['member_number' => 'BMI-CSV-001']);
        $this->assertDatabaseHas('members', ['member_number' => 'BMI-CSV-002']);
    }

    public function test_admin_can_access_branch_analytics(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.analytics'));
        $response->assertStatus(200);
        $response->assertSee('Analitik Komparasi Kinerja Cabang');
    }
}
