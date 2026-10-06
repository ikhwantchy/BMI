<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            if (!Schema::hasColumn('visits', 'business_condition')) {
                $table->text('business_condition')->nullable()->after('field_notes');
            }
            if (!Schema::hasColumn('visits', 'business_activity')) {
                $table->text('business_activity')->nullable()->after('business_condition');
            }
            if (!Schema::hasColumn('visits', 'revenue_trend')) {
                $table->text('revenue_trend')->nullable()->after('business_activity');
            }
            if (!Schema::hasColumn('visits', 'business_obstacles')) {
                $table->text('business_obstacles')->nullable()->after('revenue_trend');
            }
            if (!Schema::hasColumn('visits', 'field_findings')) {
                $table->text('field_findings')->nullable()->after('business_obstacles');
            }
            if (!Schema::hasColumn('visits', 'action_plan')) {
                $table->text('action_plan')->nullable()->after('field_findings');
            }
            if (!Schema::hasColumn('visits', 'improvement_target')) {
                $table->text('improvement_target')->nullable()->after('action_plan');
            }
        });

        // Upgrade status ENUM on MySQL to support workflow states
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `visits` MODIFY COLUMN `status` ENUM('scheduled', 'in_progress', 'waiting_validation', 'needs_revision', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn([
                'business_condition',
                'business_activity',
                'revenue_trend',
                'business_obstacles',
                'field_findings',
                'action_plan',
                'improvement_target',
            ]);
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `visits` MODIFY COLUMN `status` ENUM('scheduled', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled'");
        }
    }
};
