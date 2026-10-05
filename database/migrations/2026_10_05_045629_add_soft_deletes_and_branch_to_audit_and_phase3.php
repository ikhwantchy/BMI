<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Soft deletes for evaluations
        if (! Schema::hasColumn('evaluations', 'deleted_at')) {
            Schema::table('evaluations', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // 2. Soft deletes for coaching_recommendations
        if (! Schema::hasColumn('coaching_recommendations', 'deleted_at')) {
            Schema::table('coaching_recommendations', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // 3. Soft deletes for coaching_followups
        if (! Schema::hasColumn('coaching_followups', 'deleted_at')) {
            Schema::table('coaching_followups', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // 4. Upgrade evaluations status enum to include needs_revision (if MySQL)
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `evaluations` MODIFY COLUMN `status` ENUM('draft', 'waiting_validation', 'needs_revision', 'validated', 'rejected') NOT NULL DEFAULT 'draft'");
        }

        // 5. Add branch_id and extra context to audit_logs
        Schema::table('audit_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('audit_logs', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('user_id')
                      ->comment('Cabang pengguna pada saat aksi dilakukan');
                $table->index('branch_id');
            }
            if (! Schema::hasColumn('audit_logs', 'context')) {
                $table->string('context', 100)->nullable()->after('user_agent')
                      ->comment('Informasi tambahan: reason, role, dsb');
            }
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('coaching_recommendations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('coaching_followups', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['branch_id', 'context']);
        });
    }
};
