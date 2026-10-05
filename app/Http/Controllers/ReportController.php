<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Evaluation;
use App\Models\Member;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Dashboard laporan ringkasan eksekutif.
     */
    public function index()
    {
        $totalEvaluations = Evaluation::count();
        $validatedCount   = Evaluation::validated()->count();

        // Distribusi rekomendasi dari evaluasi tervalidasi
        $distribution = Evaluation::validated()
            ->select('recommendation', DB::raw('count(*) as total'))
            ->whereNotNull('recommendation')
            ->groupBy('recommendation')
            ->get()
            ->keyBy('recommendation');

        // Total anggota & usaha aktif
        $totalMembers    = Member::where('membership_status', 'active')->count();
        $totalBusinesses = Business::where('status', 'active')->count();
        $totalVisits     = Visit::count();

        // Rata-rata skor dari evaluasi tervalidasi
        $avgScore = Evaluation::validated()->avg('total_score') ?? 0;

        // Evaluasi per bulan (12 bulan terakhir)
        $monthlyTrend = Evaluation::select(
                DB::raw("strftime('%Y-%m', created_at) as month"),
                DB::raw('count(*) as total'),
                DB::raw('avg(total_score) as avg_score')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('reports.index', compact(
            'totalEvaluations', 'validatedCount', 'distribution',
            'totalMembers', 'totalBusinesses', 'totalVisits',
            'avgScore', 'monthlyTrend'
        ));
    }

    /**
     * Laporan evaluasi per periode dengan filter.
     */
    public function evaluations(Request $request)
    {
        $query = Evaluation::with(['business.member', 'submittedBy', 'validatedBy'])
            ->validated();

        if ($request->period) {
            $query->whereHas('visit', fn($q) => $q->where('evaluation_period', $request->period));
        }

        if ($request->recommendation) {
            $query->where('recommendation', $request->recommendation);
        }

        $evaluations = $query->orderByDesc('validated_at')->paginate(30)->withQueryString();

        $stats = [
            'avg_score'  => $query->avg('total_score') ?? 0,
            'max_score'  => $query->max('total_score') ?? 0,
            'min_score'  => $query->min('total_score') ?? 0,
            'total'      => $query->count(),
        ];

        return view('reports.evaluations', compact('evaluations', 'stats'));
    }

    /**
     * Laporan distribusi rekomendasi.
     */
    public function recommendations(Request $request)
    {
        $query = Evaluation::validated()->whereNotNull('recommendation');

        if ($request->period) {
            $query->whereHas('visit', fn($q) => $q->where('evaluation_period', $request->period));
        }

        $distribution = $query->select('recommendation', DB::raw('count(*) as total'))
            ->groupBy('recommendation')
            ->get();

        $recentEvaluations = Evaluation::validated()
            ->with(['business.member'])
            ->whereNotNull('recommendation')
            ->when($request->period, fn($q) =>
                $q->whereHas('visit', fn($vq) => $vq->where('evaluation_period', $request->period))
            )
            ->when($request->recommendation, fn($q, $r) => $q->where('recommendation', $r))
            ->latest('validated_at')
            ->paginate(20)
            ->withQueryString();

        return view('reports.recommendations', compact('distribution', 'recentEvaluations'));
    }

    /**
     * Export evaluasi ke CSV.
     */
    public function export(Request $request)
    {
        $evaluations = Evaluation::with(['business.member', 'visit', 'submittedBy', 'validatedBy'])
            ->validated()
            ->when($request->period, fn($q) =>
                $q->whereHas('visit', fn($vq) => $vq->where('evaluation_period', $request->period))
            )
            ->orderBy('validated_at')
            ->get();

        $filename = 'laporan-evaluasi-' . ($request->period ?? now()->format('Y-m')) . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($evaluations) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No. Anggota', 'Nama Anggota', 'Nama Usaha', 'Jenis Usaha',
                'Periode', 'Skor Akhir', 'Rekomendasi',
                'Status', 'Tanggal Submit', 'Divalidasi Oleh', 'Tanggal Validasi',
                'Catatan Manajer',
            ]);

            foreach ($evaluations as $eval) {
                fputcsv($file, [
                    $eval->business->member->member_number ?? '-',
                    $eval->business->member->full_name ?? '-',
                    $eval->business->name ?? '-',
                    $eval->business->business_type ?? '-',
                    $eval->visit?->evaluation_period ?? '-',
                    number_format($eval->total_score, 2),
                    $eval->recommendation?->label() ?? '-',
                    $eval->status->label(),
                    $eval->submitted_at?->format('d/m/Y') ?? '-',
                    $eval->validatedBy?->name ?? '-',
                    $eval->validated_at?->format('d/m/Y') ?? '-',
                    $eval->validator_notes ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
