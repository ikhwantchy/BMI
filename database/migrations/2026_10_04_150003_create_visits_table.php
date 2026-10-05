<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('officer_id')->constrained('users')->restrictOnDelete();
            $table->date('visit_date');
            $table->string('evaluation_period', 7)->comment('Format: YYYY-MM (bulan evaluasi)');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('field_notes')->nullable()->comment('Catatan petugas lapangan');
            $table->timestamps();
            $table->softDeletes();

            $table->index('business_id');
            $table->index('officer_id');
            $table->index('visit_date');
            $table->index('evaluation_period');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
