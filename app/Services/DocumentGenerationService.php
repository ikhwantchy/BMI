<?php

namespace App\Services;

use App\Models\Business;
use App\Models\DocumentHistory;
use App\Models\Evaluation;
use App\Models\FeasibilityAssessment;
use App\Models\FinancingAnalysis;
use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DocumentGenerationService
{
    /**
     * Generate PDF untuk Dokumen Analisis Pembiayaan
     */
    public function generateFinancingAnalysisPdf(FinancingAnalysis $analysis, bool $recordHistory = true): \Barryvdh\DomPDF\PDF
    {
        $analysis->loadMissing(['member.branch', 'business', 'analyst', 'validator']);

        if ($recordHistory) {
            $this->recordHistory(
                documentType: 'financing_analysis',
                documentNumber: 'DOC-' . $analysis->analysis_number,
                referenceId: $analysis->id,
                memberId: $analysis->member_id,
                businessId: $analysis->business_id,
                snapshotData: $analysis->toArray()
            );
        }

        return Pdf::loadView('documents.financing-analysis', compact('analysis'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);
    }

    /**
     * Generate PDF untuk Dokumen Uji Kelayakan
     */
    public function generateFeasibilityAssessmentPdf(FeasibilityAssessment $assessment, bool $recordHistory = true): \Barryvdh\DomPDF\PDF
    {
        $assessment->loadMissing(['member.branch', 'surveyor', 'validator']);

        if ($recordHistory) {
            $this->recordHistory(
                documentType: 'feasibility_assessment',
                documentNumber: 'DOC-' . $assessment->assessment_number,
                referenceId: $assessment->id,
                memberId: $assessment->member_id,
                businessId: null,
                snapshotData: $assessment->toArray()
            );
        }

        return Pdf::loadView('documents.feasibility-assessment', compact('assessment'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);
    }

    /**
     * Generate PDF untuk Dokumen Hasil Evaluasi Usaha & Pembinaan
     */
    public function generateBusinessEvaluationPdf(Evaluation $evaluation, bool $recordHistory = true): \Barryvdh\DomPDF\PDF
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

        if ($recordHistory) {
            $this->recordHistory(
                documentType: 'business_evaluation',
                documentNumber: $docNumber,
                referenceId: $evaluation->id,
                memberId: $evaluation->business->member_id,
                businessId: $evaluation->business_id,
                snapshotData: $evaluation->toArray()
            );
        }

        return Pdf::loadView('documents.business-evaluation', compact('evaluation', 'docNumber'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);
    }

    /**
     * Catat riwayat dokumen yang digenerate demi ketertelusuran (Traceability)
     */
    protected function recordHistory(
        string $documentType,
        string $documentNumber,
        int $referenceId,
        int $memberId,
        ?int $businessId,
        array $snapshotData
    ): DocumentHistory {
        return DocumentHistory::create([
            'document_type'   => $documentType,
            'document_number' => $documentNumber,
            'reference_id'    => $referenceId,
            'member_id'       => $memberId,
            'business_id'     => $businessId,
            'generated_by'    => Auth::id(),
            'snapshot_data'   => $snapshotData,
        ]);
    }
}
