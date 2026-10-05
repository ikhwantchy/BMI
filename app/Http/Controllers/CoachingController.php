<?php

namespace App\Http\Controllers;

use App\Enums\CoachingStatus;
use App\Models\CoachingFollowup;
use App\Models\CoachingRecommendation;
use App\Models\Evaluation;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CoachingController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    /**
     * Daftar semua rekomendasi coaching dari evaluasi yang sudah divalidasi.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', CoachingRecommendation::class);

        $recommendations = CoachingRecommendation::with([
                'evaluation.business.member',
                'createdBy',
            ])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->user()->isOfficer(), fn($q) =>
                $q->whereHas('evaluation.visit', fn($vq) =>
                    $vq->where('officer_id', $request->user()->id)
                )
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('coaching.index', compact('recommendations'));
    }

    /**
     * Detail satu rekomendasi beserta riwayat tindak lanjut.
     */
    public function show(CoachingRecommendation $coaching)
    {
        $this->authorize('view', $coaching);

        $coaching->load([
            'evaluation.business.member',
            'evaluation.visit',
            'createdBy',
            'validatedBy',
            'followups.officer',
        ]);

        return view('coaching.show', compact('coaching'));
    }

    /**
     * Form tambah rekomendasi coaching dari evaluasi tervalidasi.
     */
    public function create(Request $request)
    {
        $this->authorize('create', CoachingRecommendation::class);

        $evaluation = Evaluation::with('business.member')->findOrFail($request->evaluation_id);

        abort_unless($evaluation->isValidated(), 422, 'Rekomendasi hanya dapat ditambahkan pada evaluasi yang sudah tervalidasi.');

        return view('coaching.create', compact('evaluation'));
    }

    /**
     * Simpan rekomendasi coaching baru.
     */
    public function store(Request $request)
    {
        $this->authorize('create', CoachingRecommendation::class);

        $validated = $request->validate([
            'evaluation_id' => ['required', 'exists:evaluations,id'],
            'category'      => ['required', 'string', 'max:100'],
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string', 'max:2000'],
            'reason'        => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status']     = CoachingStatus::Pending->value;

        $coaching = CoachingRecommendation::create($validated);
        $this->auditService->logCreate($coaching);

        return redirect()->route('coaching.show', $coaching)
            ->with('success', 'Rekomendasi pembinaan berhasil ditambahkan.');
    }

    /**
     * Form edit rekomendasi (hanya jika masih pending).
     */
    public function edit(CoachingRecommendation $coaching)
    {
        $this->authorize('update', $coaching);

        abort_if($coaching->status !== CoachingStatus::Pending, 403, 'Rekomendasi yang sudah berjalan tidak dapat diedit.');

        return view('coaching.edit', compact('coaching'));
    }

    /**
     * Update rekomendasi.
     */
    public function update(Request $request, CoachingRecommendation $coaching)
    {
        $this->authorize('update', $coaching);

        abort_if($coaching->status !== CoachingStatus::Pending, 403, 'Rekomendasi yang sudah berjalan tidak dapat diubah.');

        $validated = $request->validate([
            'category'    => ['required', 'string', 'max:100'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'reason'      => ['nullable', 'string', 'max:1000'],
            'status'      => ['required', 'in:pending,in_progress,done'],
        ]);

        $old = $coaching->toArray();
        $coaching->update($validated);
        $this->auditService->logUpdate($coaching, $old);

        return redirect()->route('coaching.show', $coaching)
            ->with('success', 'Rekomendasi pembinaan berhasil diperbarui.');
    }

    /**
     * Tambah catatan tindak lanjut ke rekomendasi coaching.
     */
    public function addFollowup(Request $request, CoachingRecommendation $coaching)
    {
        $this->authorize('update', $coaching);
        $validated = $request->validate([
            'followup_date' => ['required', 'date'],
            'activity'      => ['required', 'string', 'max:1000'],
            'result'        => ['nullable', 'string', 'max:1000'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ]);

        $validated['evaluation_id']    = $coaching->evaluation_id;
        $validated['recommendation_id'] = $coaching->id;
        $validated['officer_id']        = Auth::id();

        $followup = CoachingFollowup::create($validated);

        // Jika status masih pending dan ada followup, ubah ke in_progress
        if ($coaching->status === CoachingStatus::Pending) {
            $coaching->update(['status' => CoachingStatus::InProgress]);
        }

        $this->auditService->logCreate($followup);

        return redirect()->route('coaching.show', $coaching)
            ->with('success', 'Catatan tindak lanjut berhasil ditambahkan.');
    }
}
