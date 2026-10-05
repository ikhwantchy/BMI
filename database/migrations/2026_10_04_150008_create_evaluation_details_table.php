<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
            $table->foreignId('parameter_id')->constrained('evaluation_parameters')->restrictOnDelete();
            $table->foreignId('indicator_id')->nullable()->constrained('evaluation_indicators')->nullOnDelete();
            $table->decimal('numeric_value', 15, 2)->nullable()->comment('Nilai input angka (misal: omzet)');
            $table->string('selected_value')->nullable()->comment('Nilai pilihan dari indikator select');
            $table->decimal('score', 5, 2)->comment('Nilai skor 0-100 untuk parameter ini');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('evaluation_id');
            $table->index('parameter_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_details');
    }
};
