<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index()
    {
        $this->authorizeAccess();

        $branches = Branch::withCount(['users', 'members', 'businesses'])
            ->orderBy('code')
            ->get();

        return view('branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'code'    => ['required', 'string', 'max:20', 'unique:branches,code'],
            'name'    => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $branch = Branch::create($validated);
        $this->auditService->logCreate($branch, "Cabang baru didaftarkan: {$branch->name}");

        return back()->with('success', "Cabang {$branch->name} ({$branch->code}) berhasil ditambahkan.");
    }

    public function update(Request $request, Branch $branch)
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'code'    => ['required', 'string', 'max:20', Rule::unique('branches')->ignore($branch->id)],
            'name'    => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $old = $branch->toArray();
        $branch->update($validated);
        $this->auditService->logUpdate($branch, $old, "Data cabang diperbarui");

        return back()->with('success', "Data cabang {$branch->name} berhasil diperbarui.");
    }

    private function authorizeAccess(): void
    {
        $user = Auth::user();
        if (!$user->can('branches.manage') && !$user->hasAnyRole(['system_admin', 'pengurus', 'pengawas', 'manajer'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function authorizeManage(): void
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['system_admin', 'pengurus'])) {
            abort(403, 'Hanya Super Admin atau Pengurus yang dapat mengelola data cabang.');
        }
    }
}
