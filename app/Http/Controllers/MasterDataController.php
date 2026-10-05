<?php

namespace App\Http\Controllers;

use App\Models\MasterData;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MasterDataController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $this->authorizeAccess();

        $selectedCategory = $request->get('category', 'business_type');

        $categories = [
            'business_type'     => 'Jenis Usaha',
            'obstacle_category' => 'Kategori Kendala',
            'coaching_type'     => 'Jenis Pembinaan',
        ];

        $items = MasterData::where('category', $selectedCategory)
            ->orderBy('name')
            ->get();

        return view('master.index', compact('items', 'categories', 'selectedCategory'));
    }

    public function store(Request $request)
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'category'    => ['required', 'in:business_type,obstacle_category,coaching_type'],
            'code'        => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('master_data')->where('category', $request->category)],
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $item = MasterData::create($validated);
        $this->auditService->logCreate($item, "Master data baru ditambahkan: {$item->name} ({$item->category})");

        return back()->with('success', "Master data {$item->name} berhasil ditambahkan.");
    }

    public function update(Request $request, MasterData $master)
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'code'        => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('master_data')->where('category', $master->category)->ignore($master->id)],
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active'   => ['required', 'boolean'],
        ]);

        $old = $master->toArray();
        $master->update($validated);
        $this->auditService->logUpdate($master, $old, "Master data diperbarui");

        return back()->with('success', "Master data {$master->name} berhasil diperbarui.");
    }

    private function authorizeAccess(): void
    {
        $user = Auth::user();
        $allowed = ['system_admin', 'pengurus', 'manajer', 'pengawas'];
        if (!in_array($user->role, $allowed) && !$user->hasAnyRole($allowed)) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function authorizeManage(): void
    {
        $user = Auth::user();
        $allowed = ['system_admin', 'pengurus'];
        if (!in_array($user->role, $allowed) && !$user->hasAnyRole($allowed)) {
            abort(403, 'Hanya Administrator atau Pengurus yang dapat mengelola master data.');
        }
    }
}
