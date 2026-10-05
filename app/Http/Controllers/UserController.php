<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $this->authorizeAccess();
        $currentUser = Auth::user();

        $query = User::with(['branch', 'roles'])->orderBy('name');

        // Branch scope
        if ($currentUser->branch_id && !$currentUser->hasAnyRole(['system_admin', 'pengurus', 'pengawas'])) {
            $query->where('branch_id', $currentUser->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();
        $branches = Branch::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'branches', 'roles'));
    }

    public function create()
    {
        $this->authorizeManage();
        $branches = Branch::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        if ($roles->isEmpty()) {
            $roles = collect([
                (object)['name' => 'petugas_lapangan'],
                (object)['name' => 'asisten_manajer'],
                (object)['name' => 'manajer'],
                (object)['name' => 'pengurus'],
                (object)['name' => 'pengawas'],
                (object)['name' => 'system_admin'],
            ]);
        }

        return view('users.create', compact('branches', 'roles'));
    }

    public function store(Request $request)
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'username'  => ['required', 'string', 'max:50', 'unique:users,username'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:8'],
            'role'      => ['required', 'string'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'status'    => ['required', 'in:active,inactive'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        try {
            $user->syncRoles([$validated['role']]);
        } catch (\Throwable $e) {}

        $this->auditService->logCreate($user, "Pengguna baru dibuat dengan role {$validated['role']}");

        return redirect()->route('users.index')->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    public function edit(User $user)
    {
        $this->authorizeManage();
        $branches = Branch::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        if ($roles->isEmpty()) {
            $roles = collect([
                (object)['name' => 'petugas_lapangan'],
                (object)['name' => 'asisten_manajer'],
                (object)['name' => 'manajer'],
                (object)['name' => 'pengurus'],
                (object)['name' => 'pengawas'],
                (object)['name' => 'system_admin'],
            ]);
        }

        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'username'  => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'  => ['nullable', 'string', 'min:8'],
            'role'      => ['required', 'string'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'status'    => ['required', 'in:active,inactive'],
        ]);

        $oldRoles = $user->getRoleNames()->toArray();
        $oldValues = $user->only(['name', 'username', 'email', 'role', 'status', 'branch_id']);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        try {
            $user->syncRoles([$validated['role']]);
        } catch (\Throwable $e) {}

        $newRoles = [$validated['role']];
        if ($oldRoles !== $newRoles) {
            $this->auditService->logRoleChanged($user->id, $oldRoles, $newRoles);
        }

        $this->auditService->logUpdate($user, $oldValues, "Data pengguna diperbarui");

        return redirect()->route('users.index')->with('success', "Pengguna {$user->name} berhasil diperbarui.");
    }

    public function toggleStatus(User $user)
    {
        $this->authorizeManage();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $this->auditService->log('update', User::class, $user->id, [], ['status' => $newStatus], "Status pengguna diubah menjadi {$newStatus}");

        return back()->with('success', "Status akun {$user->name} diubah menjadi {$newStatus}.");
    }

    private function authorizeAccess(): void
    {
        $user = Auth::user();
        if (!$user->checkRole(['system_admin', 'manajer', 'pengurus', 'pengawas'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function authorizeManage(): void
    {
        $user = Auth::user();
        if (!$user->checkRole(['system_admin', 'manajer', 'pengurus'])) {
            abort(403, 'Hanya Administrator, Pengurus, atau Manajer yang dapat mengelola pengguna.');
        }
    }
}
