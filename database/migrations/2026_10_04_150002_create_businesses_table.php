<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('name');
            $table->string('business_type');
            $table->text('address')->nullable();
            $table->unsignedSmallInteger('business_age_months')->nullable()->comment('Lama usaha dalam bulan');
            $table->unsignedBigInteger('initial_capital')->nullable()->comment('Modal awal dalam rupiah');
            $table->text('products_services')->nullable()->comment('Produk/jasa yang dijual');
            $table->text('initial_condition')->nullable()->comment('Kondisi awal usaha saat bergabung');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('member_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
