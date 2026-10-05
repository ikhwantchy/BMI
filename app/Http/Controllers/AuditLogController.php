<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $query = $this->buildQuery($request);
        $logs = $query->paginate(25)->withQueryString();

        $branches = Branch::orderBy('name')->get();
        $actions = [
            'login'        => 'Masuk (Login)',
            'failed_login' => 'Gagal Masuk (Failed Login)',
            'logout'       => 'Keluar (Logout)',
            'create'       => 'Buat Data (Create)',
            'update'       => 'Ubah Data (Update)',
            'delete'       => 'Hapus Data (Delete)',
            'submit'       => 'Pengajuan Validasi',
            'validate'     => 'Validasi / Setujui',
            'reject'       => 'Tolak',
            'revise'       => 'Minta Revisi',
            'role_changed' => 'Perubahan Role',
        ];

        return view('audit.index', compact('logs', 'branches', 'actions'));
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorizeAccess();

        $query = $this->buildQuery($request);
        $logs = $query->limit(5000)->get();

        $filename = 'audit_log_kopsyah_bmi_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Waktu (WIB)',
                'Aksi',
                'User',
                'Cabang',
                'Entitas',
                'ID Entitas',
                'IP Address',
                'Catatan / Konteks',
            ]);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->created_at->format('d/m/Y H:i:s'),
                    strtoupper($log->action),
                    $log->user?->name ?? 'Sistem / Anonim',
                    $log->branch?->name ?? ($log->user?->branch?->name ?? 'Pusat / Semua'),
                    class_basename($log->entity_type),
                    $log->entity_id ?? '-',
                    $log->ip_address ?? '-',
                    $log->context ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function authorizeAccess(): void
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        if (!$user->can('audit.view') && !$user->hasAnyRole(['manajer', 'pengawas', 'pengurus', 'system_admin'])) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Manajer, Pengawas, dan Pengurus.');
        }
    }

    private function buildQuery(Request $request)
    {
        $user = Auth::user();
        $query = AuditLog::with(['user', 'branch'])->latest();

        // Branch scope: if manager or restricted user with branch, only see their branch
        if ($user->branch_id && !$user->hasAnyRole(['system_admin', 'pengurus', 'pengawas'])) {
            $query->where(function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id)
                  ->orWhereHas('user', function ($uq) use ($user) {
                      $uq->where('branch_id', $user->branch_id);
                  });
            });
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('action')) {
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
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }
}
