<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Kode parameter, misal: kondisi_usaha');
            $table->string('name');
            $table->decimal('weight', 5, 4)->comment('Bobot 0.00-1.00, total harus = 1.00');
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_parameters');
    }
};
