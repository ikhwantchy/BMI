<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` VARCHAR(50) NOT NULL DEFAULT 'petugas_lapangan'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 50)->default('petugas_lapangan')->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('petugas_lapangan', 'asisten_manajer', 'manajer') NOT NULL DEFAULT 'petugas_lapangan'");
        }
    }
};
