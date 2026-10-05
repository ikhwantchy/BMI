<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Member::class);

        $members = Member::search($request->get('search'))
            ->when($request->get('status'), fn($q, $status) => $q->where('membership_status', $status))
            ->orderBy('full_name')
            ->paginate(20)
            ->withQueryString();

        return view('members.index', compact('members'));
    }

    public function create(): View
    {
        $this->authorize('create', Member::class);

        return view('members.create');
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $member = Member::create($request->validated());

        $this->auditService->logCreate($member);

        return redirect()->route('members.show', $member)
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function show(Member $member): View
    {
        $this->authorize('view', $member);

        $member->load(['businesses.visits', 'businesses.evaluations']);

        return view('members.show', compact('member'));
    }

    public function edit(Member $member): View
    {
        $this->authorize('update', $member);

        return view('members.edit', compact('member'));
    }

    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $old = $member->toArray();
        $member->update($request->validated());

        $this->auditService->logUpdate($member, $old);

        return redirect()->route('members.show', $member)
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $this->authorize('delete', $member);

        $old = $member->toArray();
        $member->delete();

        $this->auditService->logDelete($member);

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }

    public function history(Member $member): View
    {
        $this->authorize('view', $member);

        $member->load([
            'businesses.visits' => fn($q) => $q->orderByDesc('visit_date'),
            'businesses.evaluations' => fn($q) => $q->with(['details.parameter', 'validatedBy', 'submittedBy'])->latest()
        ]);

        return view('members.history', compact('member'));
    }
}
