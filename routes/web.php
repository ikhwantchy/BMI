<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\CoachingController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// ─── Root Landing Page ───────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Members
    Route::resource('members', MemberController::class);
    Route::get('/members/{member}/history', [MemberController::class, 'history'])->name('members.history');

    // Businesses
    Route::resource('businesses', BusinessController::class);
    Route::get('/businesses/{business}/history', [BusinessController::class, 'history'])->name('businesses.history');

    // Visits
    Route::resource('visits', VisitController::class)->except(['destroy']);
    Route::post('/visits/{visit}/complete', [VisitController::class, 'complete'])->name('visits.complete');
    Route::post('/visits/{visit}/documents', [VisitController::class, 'uploadDocument'])->name('visits.documents.store');
    Route::delete('/visits/{visit}/documents/{document}', [VisitController::class, 'deleteDocument'])->name('visits.documents.destroy');

    // Evaluations
    Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('/evaluations/{evaluation}', [EvaluationController::class, 'show'])->name('evaluations.show');
    Route::get('/visits/{visit}/evaluation/create', [EvaluationController::class, 'create'])->name('evaluations.create');
    Route::post('/visits/{visit}/evaluation', [EvaluationController::class, 'store'])->name('evaluations.store');
    Route::post('/evaluations/{evaluation}/calculate', [EvaluationController::class, 'calculate'])->name('evaluations.calculate');
    Route::post('/evaluations/{evaluation}/submit', [EvaluationController::class, 'submit'])->name('evaluations.submit');
    Route::post('/evaluations/{evaluation}/validate', [EvaluationController::class, 'validate'])->name('evaluations.validate');
    Route::post('/evaluations/{evaluation}/reject', [EvaluationController::class, 'reject'])->name('evaluations.reject');
    Route::post('/evaluations/{evaluation}/revise', [EvaluationController::class, 'revise'])->name('evaluations.revise');

    // Coaching / Tindak Lanjut Pembinaan
    Route::get('/coaching', [CoachingController::class, 'index'])->name('coaching.index');
    Route::get('/coaching/create', [CoachingController::class, 'create'])->name('coaching.create');
    Route::post('/coaching', [CoachingController::class, 'store'])->name('coaching.store');
    Route::get('/coaching/{coaching}', [CoachingController::class, 'show'])->name('coaching.show');
    Route::get('/coaching/{coaching}/edit', [CoachingController::class, 'edit'])->name('coaching.edit');
    Route::put('/coaching/{coaching}', [CoachingController::class, 'update'])->name('coaching.update');
    Route::post('/coaching/{coaching}/followup', [CoachingController::class, 'addFollowup'])->name('coaching.followup');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/evaluations', [ReportController::class, 'evaluations'])->name('reports.evaluations');
    Route::get('/reports/recommendations', [ReportController::class, 'recommendations'])->name('reports.recommendations');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Audit Trail & Keamanan
    Route::get('/audit', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit.index');
    Route::get('/audit/export', [\App\Http\Controllers\AuditLogController::class, 'export'])->name('audit.export');

    // Manajemen Pengguna
    Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show', 'destroy']);
    Route::patch('/users/{user}/toggle', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle');

    // Manajemen Cabang
    Route::get('/branches', [\App\Http\Controllers\BranchController::class, 'index'])->name('branches.index');
    Route::post('/branches', [\App\Http\Controllers\BranchController::class, 'store'])->name('branches.store');
    Route::put('/branches/{branch}', [\App\Http\Controllers\BranchController::class, 'update'])->name('branches.update');
});

// ─── Health Check Monitoring ─────────────────────────────────────────────────
Route::get('/health', function () {
    $dbOk = false;
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbOk = true;
    } catch (\Throwable $e) {}

    $storageOk = is_writable(storage_path());

    $status = ($dbOk && $storageOk) ? 200 : 503;

    return response()->json([
        'status'    => ($dbOk && $storageOk) ? 'healthy' : 'degraded',
        'timestamp' => now()->toIso8601String(),
        'checks'    => [
            'database' => $dbOk ? 'connected' : 'unreachable',
            'storage'  => $storageOk ? 'writable' : 'read-only',
        ],
    ], $status);
})->name('health');
