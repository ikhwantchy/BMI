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

// ─── Root Route ───────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('members.index');
    }
    return redirect()->route('login');
})->name('home');

// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard (redirected to members)
    Route::get('/dashboard', function () {
        return redirect()->route('members.index');
    })->name('dashboard');

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
    Route::get('/reports/analytics', [ReportController::class, 'analytics'])->name('reports.analytics');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Audit Trail & Keamanan
    Route::get('/audit', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit.index');
    Route::get('/audit/export', [\App\Http\Controllers\AuditLogController::class, 'export'])->name('audit.export');

    // Log Keamanan (percobaan login gagal & aktivitas mencurigakan)
    Route::get('/security/logs', [\App\Http\Controllers\SecurityController::class, 'logs'])->name('security.logs');

    // Cadangan Data (Backup Database) & Google Drive Sync
    Route::get('/security/backup', [\App\Http\Controllers\SecurityController::class, 'backup'])->name('security.backup');
    Route::post('/security/backup/create', [\App\Http\Controllers\SecurityController::class, 'createBackup'])->name('security.backup.create');
    Route::get('/security/backup/download/{filename}', [\App\Http\Controllers\SecurityController::class, 'downloadBackup'])->name('security.backup.download')->where('filename', '[A-Za-z0-9_\-\.]+');
    Route::delete('/security/backup/delete/{filename}', [\App\Http\Controllers\SecurityController::class, 'deleteBackup'])->name('security.backup.delete')->where('filename', '[A-Za-z0-9_\-\.]+');
    Route::post('/security/backup/gdrive', [\App\Http\Controllers\SecurityController::class, 'backupToGdrive'])->name('security.backup.gdrive');
    Route::post('/security/backup/gdrive-upload/{filename}', [\App\Http\Controllers\SecurityController::class, 'uploadToGdrive'])->name('security.backup.gdrive-upload')->where('filename', '[A-Za-z0-9_\-\.]+');
    Route::post('/security/backup/gdrive-config', [\App\Http\Controllers\SecurityController::class, 'configureGdrive'])->name('security.backup.gdrive-config');
    Route::post('/security/backup/gdrive-test', [\App\Http\Controllers\SecurityController::class, 'testGdrive'])->name('security.backup.gdrive-test');

    // Sesi Aktif
    Route::get('/security/sessions', [\App\Http\Controllers\SecurityController::class, 'sessions'])->name('security.sessions');
    Route::delete('/security/sessions/{sessionId}', [\App\Http\Controllers\SecurityController::class, 'revokeSession'])->name('security.sessions.revoke');
    Route::delete('/security/sessions', [\App\Http\Controllers\SecurityController::class, 'revokeAllSessions'])->name('security.sessions.revoke-all');

    // Manajemen Pengguna
    Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show', 'destroy']);
    Route::patch('/users/{user}/toggle', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle');

    // Manajemen Cabang
    Route::get('/branches', [\App\Http\Controllers\BranchController::class, 'index'])->name('branches.index');
    Route::post('/branches', [\App\Http\Controllers\BranchController::class, 'store'])->name('branches.store');
    Route::put('/branches/{branch}', [\App\Http\Controllers\BranchController::class, 'update'])->name('branches.update');

    // Master Data
    Route::get('/master-data', [\App\Http\Controllers\MasterDataController::class, 'index'])->name('master.index');
    Route::post('/master-data', [\App\Http\Controllers\MasterDataController::class, 'store'])->name('master.store');
    Route::put('/master-data/{master}', [\App\Http\Controllers\MasterDataController::class, 'update'])->name('master.update');

    // Impor Data Massal (CSV)
    Route::get('/import', [\App\Http\Controllers\ImportController::class, 'index'])->name('import.index');
    Route::get('/import/template/{type}', [\App\Http\Controllers\ImportController::class, 'template'])->name('import.template');
    Route::post('/import/preview', [\App\Http\Controllers\ImportController::class, 'preview'])->name('import.preview');
    Route::post('/import/confirm', [\App\Http\Controllers\ImportController::class, 'confirm'])->name('import.confirm');
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
