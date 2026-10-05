<?php

namespace App\Http\Controllers;

use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use App\Models\Member;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->hasAnyRole(['manajer', 'asisten_manajer', 'pengurus', 'pengawas', 'system_admin'])) {
            return $this->managerDashboard();
        }

        return $this->officerDashboard($user);
    }

    private function officerDashboard($user): View
    {
        $todayVisits = Visit::with(['business.member'])
            ->forOfficer($user->id)
            ->today()
            ->get();

        $pendingVisits = Visit::with(['business.member'])
            ->forOfficer($user->id)
            ->pending()
            ->count();

        $totalMembers = Member::active()->count();

        $incompleteEvaluations = Evaluation::whereHas('visit', fn($q) => $q->where('officer_id', $user->id))
            ->whereIn('status', [EvaluationStatus::Draft->value, EvaluationStatus::NeedsRevision->value])
            ->count();

        $needsRevisionCount = Evaluation::whereHas('visit', fn($q) => $q->where('officer_id', $user->id))
            ->where('status', EvaluationStatus::NeedsRevision->value)
            ->count();

        return view('dashboard.officer', compact(
            'todayVisits',
            'pendingVisits',
            'totalMembers',
            'incompleteEvaluations',
            'needsRevisionCount',
        ));
    }

    private function managerDashboard(): View
    {
        $totalMembers    = Member::count();
        $totalBusinesses = \App\Models\Business::where('status', 'active')->count();

        $evaluationStats = Evaluation::selectRaw('recommendation, COUNT(*) as count')
            ->where('status', EvaluationStatus::Validated->value)
            ->groupBy('recommendation')
            ->pluck('count', 'recommendation')
            ->toArray();

        $pendingValidations = Evaluation::waitingValidation()->count();

        $needsRevisionCount = Evaluation::where('status', EvaluationStatus::NeedsRevision->value)->count();

        $recentEvaluations = Evaluation::with(['business.member', 'submittedBy'])
            ->latest()
            ->take(10)
            ->get();

        // Trend evaluasi per bulan — DB driver compatible (SQLite for tests / MySQL for prod)
        $driver = DB::connection()->getDriverName();
        $dateExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $monthlyTrend = Evaluation::selectRaw(
                "{$dateExpr} as month,
                 COUNT(*) as total,
                 AVG(total_score) as avg_score"
            )
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard.manager', compact(
            'totalMembers',
            'totalBusinesses',
            'evaluationStats',
            'pendingValidations',
            'needsRevisionCount',
            'recentEvaluations',
            'monthlyTrend',
        ));
    }
}
