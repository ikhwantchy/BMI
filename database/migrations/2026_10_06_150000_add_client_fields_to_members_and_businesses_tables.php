<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->after('full_name')->index();
            $table->string('birth_place_date', 100)->nullable()->after('nik');
            $table->string('marital_status', 30)->nullable()->after('birth_place_date');
            $table->string('education', 50)->nullable()->after('marital_status');
            $table->string('rembug_pusat', 100)->nullable()->after('education')->index();
            $table->unsignedSmallInteger('registration_year')->nullable()->after('rembug_pusat');
            $table->string('spouse_name', 150)->nullable()->after('registration_year');
            $table->string('spouse_nik', 20)->nullable()->after('spouse_name');
            $table->string('spouse_phone', 20)->nullable()->after('spouse_nik');
            $table->string('spouse_occupation', 100)->nullable()->after('spouse_phone');
            $table->unsignedBigInteger('spouse_income')->nullable()->default(0)->after('spouse_occupation');
            $table->unsignedTinyInteger('dependents_count')->default(0)->after('spouse_income');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->unsignedBigInteger('monthly_turnover')->nullable()->default(0)->after('initial_capital');
            $table->unsignedBigInteger('daily_turnover')->nullable()->default(0)->after('monthly_turnover');
            $table->unsignedBigInteger('net_monthly_income')->nullable()->default(0)->after('daily_turnover');
            $table->unsignedSmallInteger('workforce_count')->default(0)->after('net_monthly_income');
            $table->unsignedSmallInteger('start_year')->nullable()->after('workforce_count');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'monthly_turnover',
                'daily_turnover',
                'net_monthly_income',
                'workforce_count',
                'start_year',
            ]);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'nik',
                'birth_place_date',
                'marital_status',
                'education',
                'rembug_pusat',
                'registration_year',
                'spouse_name',
                'spouse_nik',
                'spouse_phone',
                'spouse_occupation',
                'spouse_income',
                'dependents_count',
            ]);
        });
    }
};
