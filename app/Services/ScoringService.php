<?php

namespace App\Services;

use App\Enums\RecommendationStatus;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\EvaluationParameter;
use Illuminate\Support\Collection;

/**
 * ScoringService
 *
 * Menghitung nilai evaluasi berdasarkan parameter dan bobot yang dikonfigurasi di database.
 * Formula: Final Score = Σ (score_parameter × weight_parameter)
 *
 * OPEN ITEM: indikator rinci dan range skor per indikator belum final — dibuat configurable via DB.
 */
class ScoringService
{
    /**
     * Hitung total score dari detail evaluasi yang sudah ada.
     */
    public function calculate(Evaluation $evaluation): float
    {
        $details    = $evaluation->details()->with('parameter')->get();
        $parameters = EvaluationParameter::active()->get()->keyBy('id');

        $totalScore = 0.0;

        foreach ($details as $detail) {
            $parameter = $parameters->get($detail->parameter_id);

            if (! $parameter) {
                continue;
            }

            $totalScore += (float) $detail->score * (float) $parameter->weight;
        }

        return round($totalScore, 2);
    }

    /**
     * Tentukan status rekomendasi berdasarkan total score.
     * Threshold dikonfigurasi lewat config/scoring.php agar dapat diubah tanpa edit kode.
     */
    public function determineRecommendation(float $score): RecommendationStatus
    {
        $thresholds = config('scoring.recommendation_thresholds', [
            'recommended'        => 80,
            'continued_coaching' => 60,
        ]);

        if ($score >= $thresholds['recommended']) {
            return RecommendationStatus::Recommended;
        }

        if ($score >= $thresholds['continued_coaching']) {
            return RecommendationStatus::ContinuedCoaching;
        }

        return RecommendationStatus::NotRecommended;
    }

    /**
     * Generate alasan rekomendasi berdasarkan parameter dengan skor terendah.
     */
    public function generateReason(Evaluation $evaluation, RecommendationStatus $recommendation): string
    {
        $details = $evaluation->details()->with('parameter')->get();

        if ($details->isEmpty()) {
            return 'Tidak ada data evaluasi yang cukup untuk menghasilkan alasan.';
        }

        $lowest = $details->sortBy('score')->first();

        return match ($recommendation) {
            RecommendationStatus::Recommended => sprintf(
                'Usaha anggota menunjukkan kinerja baik dengan total skor %.2f. Usaha memenuhi seluruh kriteria pembinaan.',
                $evaluation->total_score
            ),
            RecommendationStatus::ContinuedCoaching => sprintf(
                'Usaha anggota memerlukan pembinaan lanjutan (skor: %.2f). Parameter "%s" memerlukan perhatian khusus.',
                $evaluation->total_score,
                $lowest?->parameter?->name ?? '-'
            ),
            RecommendationStatus::NotRecommended => sprintf(
                'Usaha anggota belum memenuhi standar koperasi (skor: %.2f). Parameter "%s" perlu perbaikan mendasar.',
                $evaluation->total_score,
                $lowest?->parameter?->name ?? '-'
            ),
        };
    }

    /**
     * Hitung dan simpan total score ke evaluasi.
     */
    public function applyScore(Evaluation $evaluation): Evaluation
    {
        $score          = $this->calculate($evaluation);
        $recommendation = $this->determineRecommendation($score);
        $reason         = $this->generateReason(
            $evaluation->load('details.parameter'),
            $recommendation
        );

        $evaluation->update([
            'total_score'           => $score,
            'recommendation'        => $recommendation,
            'recommendation_reason' => $reason,
        ]);

        return $evaluation->fresh();
    }
}
