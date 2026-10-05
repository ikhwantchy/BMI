<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coaching_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
            $table->foreignId('recommendation_id')->nullable()->constrained('coaching_recommendations')->nullOnDelete();
            $table->date('followup_date');
            $table->text('activity')->comment('Kegiatan tindak lanjut yang dilakukan');
            $table->text('result')->nullable()->comment('Hasil tindak lanjut');
            $table->text('notes')->nullable();
            $table->foreignId('officer_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('evaluation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaching_followups');
    }
};
