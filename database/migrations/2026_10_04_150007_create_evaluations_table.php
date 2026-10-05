<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->unique()->constrained('visits')->restrictOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->restrictOnDelete();
            $table->enum('status', ['draft', 'waiting_validation', 'validated', 'rejected'])->default('draft');
            $table->decimal('total_score', 5, 2)->nullable()->comment('Nilai akhir 0-100');
            $table->enum('recommendation', ['recommended', 'continued_coaching', 'not_recommended'])->nullable();
            $table->text('recommendation_reason')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->text('validator_notes')->nullable();
            $table->timestamps();

            $table->index('business_id');
            $table->index('status');
            $table->index('recommendation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
