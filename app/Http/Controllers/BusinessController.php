<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Member;
use App\Services\AuditService;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $businesses = Business::with('member')
            ->when($request->search, fn($q, $s) => $q->search($s))
            ->when($request->member_id, fn($q, $id) => $q->where('member_id', $id))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('businesses.index', compact('businesses'));
    }

    public function create(Request $request)
    {
        $members = Member::active()->orderBy('full_name')->get();
        $selectedMember = $request->member_id ? Member::find($request->member_id) : null;

        return view('businesses.create', compact('members', 'selectedMember'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id'          => ['required', 'exists:members,id'],
            'name'               => ['required', 'string', 'max:100'],
            'business_type'      => ['required', 'string', 'max:100'],
            'address'            => ['nullable', 'string', 'max:500'],
            'business_age_months'=> ['nullable', 'integer', 'min:0'],
            'initial_capital'    => ['nullable', 'integer', 'min:0'],
            'products_services'  => ['nullable', 'string', 'max:1000'],
            'initial_condition'  => ['nullable', 'string', 'max:1000'],
        ]);

        $business = Business::create($validated);
        $this->auditService->logCreate($business);

        return redirect()->route('businesses.show', $business)
            ->with('success', 'Data usaha berhasil ditambahkan.');
    }

    public function show(Business $business)
    {
        $business->load(['member', 'visits.evaluation', 'evaluations']);

        return view('businesses.show', compact('business'));
    }

    public function edit(Business $business)
    {
        $members = Member::active()->orderBy('full_name')->get();

        return view('businesses.edit', compact('business', 'members'));
    }

    public function update(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:100'],
            'business_type'      => ['required', 'string', 'max:100'],
            'address'            => ['nullable', 'string', 'max:500'],
            'business_age_months'=> ['nullable', 'integer', 'min:0'],
            'initial_capital'    => ['nullable', 'integer', 'min:0'],
            'products_services'  => ['nullable', 'string', 'max:1000'],
            'initial_condition'  => ['nullable', 'string', 'max:1000'],
            'status'             => ['required', 'in:active,inactive'],
        ]);

        $old = $business->toArray();
        $business->update($validated);
        $this->auditService->logUpdate($business, $old);

        return redirect()->route('businesses.show', $business)
            ->with('success', 'Data usaha berhasil diperbarui.');
    }


    public function destroy(Business $business)
    {
        $old = $business->toArray();
        $business->delete();
        $this->auditService->logDelete($business);

        return redirect()->route('businesses.index')
            ->with('success', 'Data usaha berhasil dihapus.');
    }

    public function history(Business $business)
    {
        $business->load('member');

        $visits = $business->visits()
            ->with(['officer', 'evaluation'])
            ->orderByDesc('visit_date')
            ->paginate(10);

        $evaluations = $business->evaluations()
            ->with(['submittedBy', 'validatedBy'])
            ->orderByDesc('created_at')
            ->get();

        return view('businesses.history', compact('business', 'visits', 'evaluations'));
    }
}

