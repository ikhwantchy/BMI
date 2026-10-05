<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parameter_id')->constrained('evaluation_parameters')->cascadeOnDelete();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('scoring_type', ['numeric', 'select', 'calculated'])->default('numeric');
            $table->json('scoring_config')->nullable()->comment('Konfigurasi scoring: range, opsi, dll (OPEN ITEM)');
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('parameter_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_indicators');
    }
};
