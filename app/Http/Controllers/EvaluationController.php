<?php

namespace App\Http\Controllers;

use App\Enums\EvaluationStatus;
use App\Enums\VisitStatus;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\EvaluationParameter;
use App\Models\Visit;
use App\Services\AuditService;
use App\Services\ScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    public function __construct(
        private ScoringService $scoringService,
        private AuditService   $auditService,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Evaluation::class);

        $evaluations = Evaluation::with(['business.member', 'submittedBy'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->recommendation, fn($q, $r) => $q->where('recommendation', $r))
            ->when($request->user()->hasRole('petugas_lapangan'), fn($q) =>
                $q->whereHas('visit', fn($vq) => $vq->where('officer_id', $request->user()->id))
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('evaluations.index', compact('evaluations'));
    }

    public function create(Visit $visit)
    {
        $this->authorize('create', Evaluation::class);

        abort_if($visit->evaluation()->exists(), 422, 'Evaluasi untuk kunjungan ini sudah ada.');
        abort_if($visit->status !== VisitStatus::Completed, 422, 'Kunjungan harus diselesaikan sebelum membuat evaluasi.');

        $parameters = EvaluationParameter::active()->with('indicators')->get();

        return view('evaluations.create', compact('visit', 'parameters'));
    }

    public function store(Request $request, Visit $visit)
    {
        $this->authorize('create', Evaluation::class);

        abort_if($visit->evaluation()->exists(), 422, 'Evaluasi sudah ada.');

        $request->validate([
            'scores'   => ['required', 'array'],
            'scores.*' => ['required', 'numeric', 'min:0', 'max:100'],
            'notes.*'  => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $visit) {
            $evaluation = Evaluation::create([
                'visit_id'    => $visit->id,
                'business_id' => $visit->business_id,
                'status'      => EvaluationStatus::Draft,
                'branch_id'   => auth()->user()->branch_id,
            ]);

            foreach ($request->scores as $parameterId => $score) {
                EvaluationDetail::create([
                    'evaluation_id' => $evaluation->id,
                    'parameter_id'  => $parameterId,
                    'score'         => $score,
                    'notes'         => $request->notes[$parameterId] ?? null,
                ]);
            }

            $this->scoringService->applyScore($evaluation);
            $this->auditService->logCreate($evaluation);
        });

        return redirect()->route('evaluations.show', $visit->evaluation)
            ->with('success', 'Evaluasi berhasil disimpan dan skor dihitung.');
    }

    public function show(Evaluation $evaluation)
    {
        $this->authorize('view', $evaluation);

        $evaluation->load([
            'visit.business.member',
            'details.parameter',
            'submittedBy',
            'validatedBy',
            'coachingRecommendations',
        ]);

        return view('evaluations.show', compact('evaluation'));
    }

    public function calculate(Evaluation $evaluation)
    {
        $this->authorize('update', $evaluation);

        abort_unless($evaluation->isEditable(), 422, 'Hanya evaluasi draft atau perlu revisi yang dapat dihitung ulang.');

        $this->scoringService->applyScore($evaluation);

        return back()->with('success', 'Skor evaluasi berhasil dihitung ulang.');
    }

    public function submit(Evaluation $evaluation)
    {
        $this->authorize('submit', $evaluation);

        $evaluation->update([
            'status'       => EvaluationStatus::WaitingValidation,
            'submitted_by' => auth()->id(),
            'submitted_at' => now(),
        ]);

        $this->auditService->logSubmit($evaluation);

        return back()->with('success', 'Evaluasi berhasil diajukan untuk validasi.');
    }

    public function validate(Request $request, Evaluation $evaluation)
    {
        $this->authorize('approve', $evaluation);

        $request->validate(['validator_notes' => ['nullable', 'string', 'max:1000']]);

        $evaluation->update([
            'status'           => EvaluationStatus::Validated,
            'validated_by'     => auth()->id(),
            'validated_at'     => now(),
            'validator_notes'  => $request->validator_notes,
        ]);

        if ($evaluation->visit) {
            $evaluation->visit->update(['status' => VisitStatus::Completed]);
        }

        $this->auditService->logValidate($evaluation);

        return back()->with('success', 'Evaluasi berhasil divalidasi dan kunjungan ditandai selesai.');
    }

    public function reject(Request $request, Evaluation $evaluation)
    {
        $this->authorize('reject', $evaluation);

        $request->validate(['validator_notes' => ['required', 'string', 'max:1000']]);

        $evaluation->update([
            'status'          => EvaluationStatus::Rejected,
            'validated_by'    => auth()->id(),
            'validated_at'    => now(),
            'validator_notes' => $request->validator_notes,
        ]);

        if ($evaluation->visit) {
            $evaluation->visit->update(['status' => VisitStatus::Cancelled]);
        }

        $this->auditService->logReject($evaluation, $request->validator_notes);

        return back()->with('success', 'Evaluasi ditolak.');
    }

    /**
     * Minta revisi — kirim balik ke petugas dengan catatan, status NeedsRevision.
     */
    public function revise(Request $request, Evaluation $evaluation)
    {
        $this->authorize('revise', $evaluation);

        $request->validate(['validator_notes' => ['required', 'string', 'max:1000']]);

        $evaluation->update([
            'status'          => EvaluationStatus::NeedsRevision,
            'validated_by'    => auth()->id(),
            'validated_at'    => now(),
            'validator_notes' => $request->validator_notes,
        ]);

        if ($evaluation->visit) {
            $evaluation->visit->update(['status' => VisitStatus::NeedsRevision]);
        }

        $this->auditService->logRevise($evaluation, $request->validator_notes);

        return back()->with('success', 'Evaluasi dikembalikan ke petugas untuk direvisi.');
    }
}
