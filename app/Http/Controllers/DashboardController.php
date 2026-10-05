<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Member;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isManager() || $user->isAssistantManager()) {
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
            ->where('status', 'draft')
            ->count();

        return view('dashboard.officer', compact(
            'todayVisits',
            'pendingVisits',
            'totalMembers',
            'incompleteEvaluations'
        ));
    }

    private function managerDashboard(): View
    {
        $totalMembers = Member::count();
        $totalBusinesses = \App\Models\Business::where('status', 'active')->count();

        $evaluationStats = Evaluation::selectRaw('recommendation, COUNT(*) as count')
            ->where('status', 'validated')
            ->groupBy('recommendation')
            ->pluck('count', 'recommendation')
            ->toArray();

        $pendingValidations = Evaluation::waitingValidation()->count();

        $recentEvaluations = Evaluation::with(['business.member', 'submittedBy'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.manager', compact(
            'totalMembers',
            'totalBusinesses',
            'evaluationStats',
            'pendingValidations',
            'recentEvaluations'
        ));
    }
}
