<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\DocumentHistory;
use App\Models\Evaluation;
use App\Models\FeasibilityAssessment;
use App\Models\FinancingAnalysis;
use App\Models\Member;
use App\Services\DocumentGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentGenerationService $documentService
    ) {}

    /**
     * Indeks & Riwayat Dokumen Resmi Klien (Traceability)
     */
    public function index(Request $request)
    {
        $query = DocumentHistory::with(['member', 'business', 'generatedBy'])
            ->latest();

        if ($request->filled('type')) {
            $query->where('document_type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                  ->orWhereHas('member', fn($mq) => $mq->where('full_name', 'like', "%{$search}%")->orWhere('member_number', 'like', "%{$search}%"));
            });
        }

        $histories = $query->paginate(20)->withQueryString();

        // Data terbaru untuk pembuatan cepat
        $recentAnalyses    = FinancingAnalysis::with(['member', 'business'])->latest()->limit(5)->get();
        $recentAssessments = FeasibilityAssessment::with('member')->latest()->limit(5)->get();
        $recentEvaluations = Evaluation::with(['business.member'])->validated()->latest('validated_at')->limit(5)->get();

        return view('documents.index', compact('histories', 'recentAnalyses', 'recentAssessments', 'recentEvaluations'));
    }

    // ─── ANALISIS PEMBIAYAAN ──────────────────────────────────────────────────

    public function showFinancingAnalysis(FinancingAnalysis $analysis)
    {
        $analysis->loadMissing(['member.branch', 'business', 'analyst', 'validator']);
        return view('documents.financing-analysis', compact('analysis'));
    }

    public function pdfFinancingAnalysis(FinancingAnalysis $analysis)
    {
        $pdf = $this->documentService->generateFinancingAnalysisPdf($analysis);
        $filename = 'Analisis-Pembiayaan-' . str_replace('/', '-', $analysis->analysis_number) . '.pdf';
        return $pdf->stream($filename);
    }

    public function createFinancingAnalysis(Request $request)
    {
        $selectedMember = null;
        if ($request->filled('member_id')) {
            $selectedMember = Member::with(['businesses', 'latestFeasibilityAssessment'])->findOrFail($request->member_id);
        }

        $members = Member::orderBy('full_name')->get();
        return view('documents.create-financing-analysis', compact('members', 'selectedMember'));
    }

    public function storeFinancingAnalysis(Request $request)
    {
        $validated = $request->validate([
            'member_id'                    => ['required', 'exists:members,id'],
            'business_id'                  => ['nullable', 'exists:businesses,id'],
            'analysis_date'                => ['required', 'date'],
            'proposed_amount'              => ['required', 'numeric', 'min:0'],
            'financing_purpose'            => ['nullable', 'string'],
            'last_financing_amount'        => ['nullable', 'numeric', 'min:0'],
            'approved_ceiling'             => ['nullable', 'numeric', 'min:0'],
            'investment_ceiling'           => ['nullable', 'numeric', 'min:0'],
            'financing_other_institution'  => ['nullable', 'numeric', 'min:0'],
            'business_type'                => ['nullable', 'string'],
            'monthly_turnover'             => ['nullable', 'numeric', 'min:0'],
            'daily_turnover'               => ['nullable', 'numeric', 'min:0'],
            'workforce_count'              => ['nullable', 'integer', 'min:0'],
            'business_assets_estimate'     => ['nullable', 'numeric', 'min:0'],
            'savings_amount'               => ['nullable', 'numeric', 'min:0'],
            'electronic_assets_estimate'   => ['nullable', 'numeric', 'min:0'],
            'vehicle_assets_estimate'      => ['nullable', 'numeric', 'min:0'],
            'business_income'              => ['nullable', 'numeric', 'min:0'],
            'spouse_income'                => ['nullable', 'numeric', 'min:0'],
            'household_expenses'           => ['nullable', 'numeric', 'min:0'],
            'business_expenses'            => ['nullable', 'numeric', 'min:0'],
            'other_installments'           => ['nullable', 'numeric', 'min:0'],
            'conclusion'                   => ['required', 'in:layak,layak_bersyarat,tidak_layak'],
            'notes'                        => ['nullable', 'string'],
        ]);

        $member = Member::findOrFail($validated['member_id']);

        $analysisNumber = 'AP-' . date('Ym') . '-' . str_pad((string) (FinancingAnalysis::count() + 1), 3, '0', STR_PAD_LEFT);

        $analysis = new FinancingAnalysis(array_merge($validated, [
            'analysis_number'   => $analysisNumber,
            'branch_id'         => $member->branch_id,
            'user_id'           => Auth::id(),
            'spouse_name'       => $member->spouse_name,
            'rembug_pusat'      => $member->rembug_pusat,
            'registration_year' => $member->registration_year,
            'status'            => 'validated', // siap cetak
            'validated_by'      => Auth::id(),
            'validated_at'      => now(),
        ]));

        $analysis->calculateFinancials();
        $analysis->save();

        return redirect()->route('documents.financing-analysis.show', $analysis->id)
            ->with('status', 'Dokumen Analisis Pembiayaan berhasil dibuat sesuai format standar BMI.');
    }

    // ─── UJI KELAYAKAN ────────────────────────────────────────────────────────

    public function showFeasibilityAssessment(FeasibilityAssessment $assessment)
    {
        $assessment->loadMissing(['member.branch', 'surveyor', 'validator']);
        return view('documents.feasibility-assessment', compact('assessment'));
    }

    public function pdfFeasibilityAssessment(FeasibilityAssessment $assessment)
    {
        $pdf = $this->documentService->generateFeasibilityAssessmentPdf($assessment);
        $filename = 'Uji-Kelayakan-' . str_replace('/', '-', $assessment->assessment_number) . '.pdf';
        return $pdf->stream($filename);
    }

    public function createFeasibilityAssessment(Request $request)
    {
        $selectedMember = null;
        if ($request->filled('member_id')) {
            $selectedMember = Member::findOrFail($request->member_id);
        }

        $members = Member::orderBy('full_name')->get();
        return view('documents.create-feasibility-assessment', compact('members', 'selectedMember'));
    }

    public function storeFeasibilityAssessment(Request $request)
    {
        $validated = $request->validate([
            'member_id'                => ['required', 'exists:members,id'],
            'assessment_date'          => ['required', 'date'],
            'nik'                      => ['nullable', 'string', 'max:20'],
            'birth_place_date'         => ['nullable', 'string'],
            'marital_status'           => ['nullable', 'string'],
            'education'                => ['nullable', 'string'],
            'rt_rw'                    => ['nullable', 'string'],
            'village'                  => ['nullable', 'string'],
            'district'                 => ['nullable', 'string'],
            'spouse_name'              => ['nullable', 'string'],
            'spouse_nik'               => ['nullable', 'string'],
            'spouse_occupation'        => ['nullable', 'string'],
            'spouse_income'            => ['nullable', 'numeric', 'min:0'],
            'spouse_phone'             => ['nullable', 'string'],
            'dependents_count'         => ['nullable', 'integer', 'min:0'],
            'schooling_children_count' => ['nullable', 'integer', 'min:0'],
            'home_ownership_status'    => ['nullable', 'string'],
            'wall_type'                => ['nullable', 'string'],
            'floor_type'               => ['nullable', 'string'],
            'roof_type'                => ['nullable', 'string'],
            'water_source'             => ['nullable', 'string'],
            'electricity_power'        => ['nullable', 'string'],
            'land_home_assets'         => ['nullable', 'string'],
            'vehicle_assets'           => ['nullable', 'string'],
            'electronic_assets'        => ['nullable', 'string'],
            'savings_gold_assets'      => ['nullable', 'string'],
            'community_relation'       => ['nullable', 'string'],
            'rembug_pusat_activity'    => ['nullable', 'string'],
            'reputation_character'     => ['nullable', 'string'],
            'result'                   => ['required', 'in:memenuhi_syarat,perlu_pertimbangan,tidak_memenuhi_syarat'],
            'notes'                    => ['nullable', 'string'],
        ]);

        $member = Member::findOrFail($validated['member_id']);

        // Update member master data with verified survey data (Data Reuse)
        $member->update(array_filter([
            'nik'               => $validated['nik'] ?? null,
            'birth_place_date'  => $validated['birth_place_date'] ?? null,
            'marital_status'    => $validated['marital_status'] ?? null,
            'education'         => $validated['education'] ?? null,
            'spouse_name'       => $validated['spouse_name'] ?? null,
            'spouse_nik'        => $validated['spouse_nik'] ?? null,
            'spouse_occupation' => $validated['spouse_occupation'] ?? null,
            'spouse_income'     => $validated['spouse_income'] ?? null,
            'spouse_phone'      => $validated['spouse_phone'] ?? null,
            'dependents_count'  => $validated['dependents_count'] ?? 0,
        ]));

        $assessmentNumber = 'UK-' . date('Ym') . '-' . str_pad((string) (FeasibilityAssessment::count() + 1), 3, '0', STR_PAD_LEFT);

        $assessment = FeasibilityAssessment::create(array_merge($validated, [
            'assessment_number' => $assessmentNumber,
            'branch_id'         => $member->branch_id,
            'user_id'           => Auth::id(),
            'status'            => 'validated',
            'validated_by'      => Auth::id(),
            'validated_at'      => now(),
        ]));

        return redirect()->route('documents.feasibility-assessment.show', $assessment->id)
            ->with('status', 'Dokumen Uji Kelayakan berhasil disimpan sesuai format standar BMI.');
    }

    // ─── EVALUASI USAHA ───────────────────────────────────────────────────────

    public function showBusinessEvaluation(Evaluation $evaluation)
    {
        $evaluation->loadMissing([
            'business.member.branch',
            'visit.documents',
            'details.parameter',
            'coachingRecommendations',
            'submittedBy',
            'validatedBy',
        ]);
        $docNumber = 'DOC-EV-' . str_replace('-', '', $evaluation->visit?->evaluation_period ?? date('Ym')) . '-' . str_pad((string) $evaluation->id, 4, '0', STR_PAD_LEFT);

        return view('documents.business-evaluation', compact('evaluation', 'docNumber'));
    }

    public function pdfBusinessEvaluation(Evaluation $evaluation)
    {
        $pdf = $this->documentService->generateBusinessEvaluationPdf($evaluation);
        $filename = 'Evaluasi-Usaha-' . ($evaluation->visit?->evaluation_period ?? date('Ym')) . '-' . $evaluation->id . '.pdf';
        return $pdf->stream($filename);
    }
}
