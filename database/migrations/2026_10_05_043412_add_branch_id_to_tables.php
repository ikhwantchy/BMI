<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'users', 'members', 'businesses', 'visits',
            'evaluations', 'coaching_recommendations', 'coaching_followups'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'users', 'members', 'businesses', 'visits',
            'evaluations', 'coaching_recommendations', 'coaching_followups'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
