<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_number')->unique()->comment('Nomor anggota koperasi');
            $table->string('full_name');
            $table->text('address');
            $table->string('phone', 20)->nullable();
            $table->enum('membership_status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('membership_status');
            $table->index('member_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
