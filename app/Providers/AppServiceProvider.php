<?php

namespace App\Providers;

use App\Models\Evaluation;
use App\Models\Member;
use App\Policies\EvaluationPolicy;
use App\Policies\MemberPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind services as singletons
        $this->app->singleton(\App\Services\ScoringService::class);
        $this->app->singleton(\App\Services\AuditService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Policies
        Gate::policy(Member::class, MemberPolicy::class);
        Gate::policy(Evaluation::class, EvaluationPolicy::class);
    }
}
