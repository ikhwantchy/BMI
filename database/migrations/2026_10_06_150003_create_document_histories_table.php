<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_histories', function (Blueprint $table) {
            $table->id();
            $table->enum('document_type', ['financing_analysis', 'feasibility_assessment', 'business_evaluation']);
            $table->string('document_number', 60)->index();
            $table->unsignedBigInteger('reference_id')->index()->comment('ID of the related analysis, assessment, or evaluation');
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('business_id')->nullable()->constrained('businesses')->nullOnDelete();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('file_path')->nullable();
            $table->json('snapshot_data')->nullable()->comment('Exact snapshot of data at generation time');
            $table->timestamps();

            $table->index(['document_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_histories');
    }
};
