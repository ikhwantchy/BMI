<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coaching_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
            $table->string('category')->comment('Kategori pembinaan, misal: keuangan, pemasaran');
            $table->string('title');
            $table->text('description');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'done'])->default('pending');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->index('evaluation_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaching_recommendations');
    }
};
