<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SecurityController extends Controller
{
    // ─── Authorization ─────────────────────────────────────────────────────────

    private function authorizeAccess(): void
    {
        $user = Auth::user();
        if (!$user) abort(401);

        if (!$user->hasAnyRole(['manajer', 'pengawas', 'pengurus', 'system_admin'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        if (!$user) abort(401);

        if (!$user->hasAnyRole(['system_admin', 'pengurus'])) {
            abort(403, 'Hanya System Admin dan Pengurus yang dapat mengakses fitur ini.');
        }
    }

    // ─── Log Keamanan ──────────────────────────────────────────────────────────

    public function logs(Request $request)
    {
        $this->authorizeAccess();

        $branches = Branch::orderBy('name')->get();

        // Security-relevant actions only
        $securityActions = ['failed_login', 'login', 'logout', 'role_changed'];

        $query = AuditLog::with(['user', 'branch'])
            ->whereIn('action', $securityActions)
            ->latest();

        // Branch scope
        $user = Auth::user();
        if ($user->branch_id && !$user->hasAnyRole(['system_admin', 'pengurus', 'pengawas'])) {
            $query->where(function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id)
                  ->orWhereHas('user', fn($uq) => $uq->where('branch_id', $user->branch_id));
            });
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('action') && in_array($request->action, $securityActions)) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('context', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        // Summary stats for the period
        $totalFailed  = (clone $query)->where('action', 'failed_login')->count();
        $totalLogin   = (clone $query)->where('action', 'login')->count();
        $totalLogout  = (clone $query)->where('action', 'logout')->count();
        $uniqueIps    = (clone $query)->where('action', 'failed_login')
                            ->distinct('ip_address')
                            ->count('ip_address');

        // Top attacker IPs (most failed logins)
        $topAttackerIps = AuditLog::query()
            ->where('action', 'failed_login')
            ->selectRaw('ip_address, count(*) as total')
            ->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $logs = $query->paginate(25)->withQueryString();

        $actions = [
            'failed_login' => 'Gagal Masuk (Failed Login)',
            'login'        => 'Berhasil Masuk (Login)',
            'logout'       => 'Keluar (Logout)',
            'role_changed' => 'Perubahan Role',
        ];

        return view('security.logs', compact(
            'logs', 'branches', 'actions',
            'totalFailed', 'totalLogin', 'totalLogout', 'uniqueIps',
            'topAttackerIps'
        ));
    }

    // ─── Backup Database ───────────────────────────────────────────────────────

    public function backup(Request $request)
    {
        $this->authorizeAdmin();

        // List existing backups
        $backupPath = storage_path('app/backups');
        $backups = [];

        if (is_dir($backupPath)) {
            $files = glob($backupPath . '/*.sql');
            foreach ($files as $file) {
                $backups[] = [
                    'filename'   => basename($file),
                    'size'       => filesize($file),
                    'created_at' => \Carbon\Carbon::createFromTimestamp(filemtime($file)),
                ];
            }
            // Sort newest first
            usort($backups, fn($a, $b) => $b['created_at']->timestamp - $a['created_at']->timestamp);
        }

        // DB connection info for display
        $dbConfig = [
            'driver'   => config('database.default'),
            'database' => config('database.connections.' . config('database.default') . '.database'),
        ];

        // Health checks
        $dbOk = false;
        try {
            DB::connection()->getPdo();
            $dbOk = true;
        } catch (\Throwable) {}

        $storageOk = is_writable(storage_path('app'));

        return view('security.backup', compact('backups', 'dbConfig', 'dbOk', 'storageOk'));
    }

    public function createBackup(Request $request)
    {
        $this->authorizeAdmin();

        $backupPath = storage_path('app/backups');
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $dbDriver = config('database.default');

        if ($dbDriver !== 'mysql' && $dbDriver !== 'mariadb') {
            return back()->with('error', 'Backup otomatis hanya mendukung MySQL/MariaDB. Gunakan tools lain untuk SQLite/PostgreSQL.');
        }

        $dbHost     = config("database.connections.{$dbDriver}.host", '127.0.0.1');
        $dbPort     = config("database.connections.{$dbDriver}.port", 3306);
        $dbName     = config("database.connections.{$dbDriver}.database");
        $dbUser     = config("database.connections.{$dbDriver}.username");
        $dbPassword = config("database.connections.{$dbDriver}.password");

        $filename   = 'backup_kopsyah_' . now()->format('Ymd_His') . '.sql';
        $outputPath = $backupPath . DIRECTORY_SEPARATOR . $filename;

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --quick --lock-tables=false %s > %s 2>&1',
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbPassword),
            escapeshellarg($dbName),
            escapeshellarg($outputPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputPath) || filesize($outputPath) < 100) {
            // Remove broken file if any
            if (file_exists($outputPath)) unlink($outputPath);
            return back()->with('error', 'Backup gagal. Pastikan mysqldump tersedia di server. Output: ' . implode(' ', $output));
        }

        // Log the backup action
        app(AuditService::class)->log('create', 'BackupFile', null, [], [], "Backup database dibuat: {$filename}");

        return back()->with('success', "Backup berhasil dibuat: {$filename} (" . number_format(filesize($outputPath) / 1024, 1) . " KB)");
    }

    public function downloadBackup(string $filename)
    {
        $this->authorizeAdmin();

        // Sanitize filename — no directory traversal
        $filename   = basename($filename);
        $backupPath = storage_path('app/backups/' . $filename);

        if (!file_exists($backupPath) || !str_ends_with($filename, '.sql')) {
            abort(404, 'File backup tidak ditemukan.');
        }

        return response()->download($backupPath);
    }

    public function deleteBackup(string $filename)
    {
        $this->authorizeAdmin();

        $filename   = basename($filename);
        $backupPath = storage_path('app/backups/' . $filename);

        if (!file_exists($backupPath) || !str_ends_with($filename, '.sql')) {
            abort(404, 'File backup tidak ditemukan.');
        }

        unlink($backupPath);

        app(AuditService::class)->log('delete', 'BackupFile', null, [], [], "File backup dihapus: {$filename}");

        return back()->with('success', "File backup {$filename} berhasil dihapus.");
    }

    // ─── Sesi Aktif ────────────────────────────────────────────────────────────

    public function sessions(Request $request)
    {
        $this->authorizeAccess();

        $currentUser = Auth::user();

        // Only system_admin / pengurus can see all sessions; others see own only
        $canViewAll = $currentUser->hasAnyRole(['system_admin', 'pengurus', 'pengawas']);

        // Fetch session data
        // Laravel stores sessions in DB (table: sessions) if SESSION_DRIVER=database
        // We'll check and fall back gracefully
        $sessions = collect();
        $sessionDriver = config('session.driver');

        if ($sessionDriver === 'database') {
            $query = DB::table('sessions')
                ->join('users', 'users.id', '=', 'sessions.user_id', 'left')
                ->select(
                    'sessions.id as session_id',
                    'sessions.user_id',
                    'sessions.ip_address',
                    'sessions.user_agent',
                    'sessions.last_activity',
                    'users.name as user_name',
                    'users.email as user_email',
                    'users.role as user_role',
                )
                ->orderByDesc('sessions.last_activity');

            if (!$canViewAll) {
                $query->where('sessions.user_id', $currentUser->id);
            }

            $sessions = $query->get()->map(function ($s) {
                $s->last_activity_dt = \Carbon\Carbon::createFromTimestamp($s->last_activity);
                $s->is_expired       = $s->last_activity_dt->diffInMinutes(now()) > config('session.lifetime', 120);
                return $s;
            });
        }

        $currentSessionId = session()->getId();

        // Stats
        $totalActive  = $sessions->filter(fn($s) => !$s->is_expired)->count();
        $totalExpired = $sessions->filter(fn($s) => $s->is_expired)->count();
        $totalUsers   = $sessions->whereNotNull('user_id')->pluck('user_id')->unique()->count();

        return view('security.sessions', compact(
            'sessions', 'sessionDriver', 'canViewAll',
            'currentSessionId', 'totalActive', 'totalExpired', 'totalUsers'
        ));
    }

    public function revokeSession(string $sessionId, Request $request)
    {
        $this->authorizeAccess();

        $currentUser = Auth::user();
        $sessionDriver = config('session.driver');

        if ($sessionDriver !== 'database') {
            return back()->with('error', 'Pencabutan sesi hanya tersedia saat session driver=database.');
        }

        // Prevent self-revocation
        if ($sessionId === session()->getId()) {
            return back()->with('error', 'Tidak dapat mencabut sesi Anda sendiri yang aktif.');
        }

        // Check ownership / authorization
        $session = DB::table('sessions')->where('id', $sessionId)->first();
        if (!$session) {
            return back()->with('error', 'Sesi tidak ditemukan.');
        }

        $canRevokeAll = $currentUser->hasAnyRole(['system_admin', 'pengurus', 'pengawas']);
        if (!$canRevokeAll && $session->user_id !== $currentUser->id) {
            abort(403);
        }

        DB::table('sessions')->where('id', $sessionId)->delete();

        app(AuditService::class)->log('update', 'Session', null, [], [], "Sesi dicabut (revoked): {$sessionId}");

        return back()->with('success', 'Sesi berhasil dicabut.');
    }

    public function revokeAllSessions(Request $request)
    {
        $this->authorizeAdmin();

        $sessionDriver = config('session.driver');
        if ($sessionDriver !== 'database') {
            return back()->with('error', 'Pencabutan sesi hanya tersedia saat session driver=database.');
        }

        $currentSessionId = session()->getId();
        $count = DB::table('sessions')
            ->where('id', '!=', $currentSessionId)
            ->delete();

        app(AuditService::class)->log('update', 'Session', null, [], [], "Semua sesi dicabut ({$count} sesi) kecuali sesi aktif admin.");

        return back()->with('success', "{$count} sesi berhasil dicabut.");
    }
}
