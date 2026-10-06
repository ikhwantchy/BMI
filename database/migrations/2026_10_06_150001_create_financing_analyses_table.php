<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financing_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('business_id')->nullable()->constrained('businesses')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Petugas Analis');
            $table->string('analysis_number', 50)->unique();
            $table->date('analysis_date');

            // A. Data Anggota (Snapshot & Context)
            $table->string('spouse_name', 150)->nullable();
            $table->string('rembug_pusat', 100)->nullable();
            $table->unsignedSmallInteger('registration_year')->nullable();

            // B. Pembiayaan
            $table->unsignedBigInteger('proposed_amount')->comment('Rencana Pengajuan');
            $table->text('financing_purpose')->nullable()->comment('Tujuan Penggunaan');
            $table->unsignedBigInteger('last_financing_amount')->default(0)->comment('Pembiayaan Terakhir');
            $table->unsignedBigInteger('approved_ceiling')->nullable()->comment('Plafon Pembiayaan Disetujui');
            $table->unsignedBigInteger('investment_ceiling')->default(0)->comment('Plafon Investasi');
            $table->unsignedBigInteger('financing_other_institution')->default(0)->comment('Pembiayaan di Tempat Lain');

            // C. Kapasitas Usaha
            $table->string('business_type', 100)->nullable();
            $table->unsignedSmallInteger('business_start_year')->nullable();
            $table->unsignedBigInteger('monthly_turnover')->default(0);
            $table->unsignedBigInteger('daily_turnover')->default(0);
            $table->unsignedSmallInteger('workforce_count')->default(0);
            $table->unsignedBigInteger('net_business_income')->default(0);

            // D. Aset dan Simpanan
            $table->unsignedBigInteger('business_assets_estimate')->default(0);
            $table->unsignedBigInteger('savings_amount')->default(0);
            $table->unsignedBigInteger('electronic_assets_estimate')->default(0);
            $table->unsignedBigInteger('vehicle_assets_estimate')->default(0);
            $table->unsignedBigInteger('total_assets_estimate')->default(0);

            // E. Keuangan (Arus Kas)
            $table->unsignedBigInteger('business_income')->default(0);
            $table->unsignedBigInteger('spouse_income')->default(0);
            $table->unsignedBigInteger('total_income')->default(0);
            $table->unsignedBigInteger('per_capita_income')->default(0);
            $table->unsignedBigInteger('household_expenses')->default(0);
            $table->unsignedBigInteger('business_expenses')->default(0);
            $table->unsignedBigInteger('other_installments')->default(0);
            $table->unsignedBigInteger('total_expenses')->default(0);

            // F. Kemampuan Anggota (Capacity to Repay)
            $table->bigInteger('saving_capacity')->default(0);
            $table->bigInteger('installment_capacity')->default(0);
            $table->enum('conclusion', ['layak', 'layak_bersyarat', 'tidak_layak'])->default('layak');
            $table->text('notes')->nullable();

            // Status & Approval Workflow
            $table->enum('status', ['draft', 'submitted', 'validated', 'rejected'])->default('draft');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->text('validator_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('member_id');
            $table->index('status');
            $table->index('analysis_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financing_analyses');
    }
};
