<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feasibility_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Petugas Verifikator');
            $table->string('assessment_number', 50)->unique();
            $table->date('assessment_date');

            // A. Data Anggota
            $table->string('nik', 20)->nullable();
            $table->string('birth_place_date', 100)->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->string('education', 50)->nullable();
            $table->string('rt_rw', 20)->nullable();
            $table->string('village', 100)->nullable();
            $table->string('district', 100)->nullable();

            // B. Data Pasangan
            $table->string('spouse_name', 150)->nullable();
            $table->string('spouse_nik', 20)->nullable();
            $table->string('spouse_occupation', 100)->nullable();
            $table->unsignedBigInteger('spouse_income')->default(0);
            $table->string('spouse_phone', 20)->nullable();

            // C. Data Anggota Keluarga & Tempat Tinggal
            $table->unsignedTinyInteger('dependents_count')->default(0);
            $table->unsignedTinyInteger('schooling_children_count')->default(0);
            $table->string('home_ownership_status', 50)->nullable()->comment('milik_sendiri, sewa, numpang, keluarga');
            $table->string('wall_type', 50)->nullable()->comment('tembok, kayu, bambu, semi_permanen');
            $table->string('floor_type', 50)->nullable()->comment('keramik, semen, tanah');
            $table->string('roof_type', 50)->nullable()->comment('genteng, asbes, seng');
            $table->string('water_source', 50)->nullable()->comment('sumur_bor, pdam, sumur_gali');
            $table->string('electricity_power', 50)->nullable()->comment('450VA, 900VA, 1300VA, >1300VA');

            // D. Aset Rumah Tangga
            $table->text('land_home_assets')->nullable();
            $table->text('vehicle_assets')->nullable();
            $table->text('electronic_assets')->nullable();
            $table->text('savings_gold_assets')->nullable();

            // E. Kelayakan Sosial & Karakter
            $table->string('community_relation', 50)->nullable()->comment('baik, cukup, kurang');
            $table->string('rembug_pusat_activity', 50)->nullable()->comment('aktif, cukup_aktif, baru');
            $table->text('reputation_character')->nullable();

            // F. Kesimpulan Uji Kelayakan
            $table->enum('result', ['memenuhi_syarat', 'perlu_pertimbangan', 'tidak_memenuhi_syarat'])->default('memenuhi_syarat');
            $table->text('notes')->nullable();

            // Status & Approval
            $table->enum('status', ['draft', 'submitted', 'validated', 'rejected'])->default('draft');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->text('validator_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('member_id');
            $table->index('status');
            $table->index('assessment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feasibility_assessments');
    }
};
