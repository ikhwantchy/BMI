<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\Business;
use App\Models\Visit;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Visit::class);

        $visits = Visit::with(['business.member', 'officer'])
            ->when($request->user()->isOfficer(), fn($q) => $q->forOfficer($request->user()->id))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->date, fn($q, $d) => $q->whereDate('visit_date', $d))
            ->orderByDesc('visit_date')
            ->paginate(20)
            ->withQueryString();

        return view('visits.index', compact('visits'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Visit::class);

        $businesses = Business::with('member')->active()->orderBy('name')->get();
        $selectedBusiness = $request->business_id ? Business::with('member')->find($request->business_id) : null;
        
        // Ambil daftar petugas lapangan untuk dipilih oleh manajer
        $officers = \App\Models\User::role('petugas_lapangan')->get();

        return view('visits.create', compact('businesses', 'selectedBusiness', 'officers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Visit::class);

        $validated = $request->validate([
            'business_id'       => ['required', 'exists:businesses,id'],
            'officer_id'        => ['required', 'exists:users,id'],
            'visit_date'        => ['required', 'date'],
            'evaluation_period' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'field_notes'       => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['status']     = VisitStatus::Scheduled->value;

        $visit = Visit::create($validated);
        $this->auditService->logCreate($visit);

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Jadwal kunjungan berhasil dibuat dan ditugaskan.');
    }

    public function show(Visit $visit)
    {
        $this->authorize('view', $visit);

        $visit->load(['business.member', 'officer', 'documents', 'evaluation.details.parameter']);
        $parameters = \App\Models\EvaluationParameter::active()->get();

        return view('visits.show', compact('visit', 'parameters'));
    }

    public function edit(Visit $visit)
    {
        $this->authorize('update', $visit);

        $businesses = Business::with('member')->active()->orderBy('name')->get();

        return view('visits.edit', compact('visit', 'businesses'));
    }

    public function update(Request $request, Visit $visit)
    {
        $this->authorize('update', $visit);

        abort_if($visit->status === VisitStatus::Completed, 403, 'Kunjungan yang sudah selesai tidak dapat diubah.');

        $validated = $request->validate([
            'visit_date'        => ['required', 'date'],
            'evaluation_period' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'field_notes'       => ['nullable', 'string', 'max:2000'],
        ]);

        $old = $visit->toArray();
        $visit->update($validated);
        $this->auditService->logUpdate($visit, $old);

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Data kunjungan berhasil diperbarui.');
    }

    public function submitExecution(Request $request, Visit $visit)
    {
        $this->authorize("update", $visit);
        $request->validate([
            "field_notes" => "nullable|string|max:2000",
            "scores" => "required|array",
            "scores.*" => "required|numeric|min:0|max:100",
            "notes.*" => "nullable|string|max:500",
            "recommendation" => "required|string|in:recommended,continued_coaching,not_recommended",
            "recommendation_reason" => "nullable|string",
        ]);

        DB::transaction(function () use ($request, $visit) {
            $visit->update([
                "field_notes" => $request->field_notes,
                "status" => \App\Enums\VisitStatus::Completed->value,
            ]);

            $evaluation = $visit->evaluation()->firstOrCreate([
                "business_id" => $visit->business_id,
                "branch_id" => auth()->user()->branch_id ?? 1,
            ]);

            $evaluation->update([
                "status" => \App\Enums\EvaluationStatus::WaitingValidation->value,
                "recommendation" => $request->recommendation,
                "recommendation_reason" => $request->recommendation_reason,
                "submitted_by" => auth()->id(),
                "submitted_at" => now(),
            ]);

            foreach ($request->scores as $parameterId => $score) {
                \App\Models\EvaluationDetail::updateOrCreate(
                    ["evaluation_id" => $evaluation->id, "parameter_id" => $parameterId],
                    ["score" => $score, "notes" => $request->notes[$parameterId] ?? null]
                );
            }
            app(\App\Services\ScoringService::class)->applyScore($evaluation);
        });

        return redirect()->route("visits.show", $visit)->with("success", "Kunjungan berhasil diselesaikan dan menunggu validasi.");
    }

    public function complete(Request $request, Visit $visit)
    {
        $this->authorize('update', $visit);

        abort_if($visit->status === VisitStatus::Completed, 403, 'Kunjungan sudah selesai.');

        $visit->update(['status' => VisitStatus::Completed]);
        $this->auditService->log('complete', Visit::class, $visit->id);

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Status kunjungan diperbarui menjadi Selesai.');
    }

    public function uploadDocument(Request $request, Visit $visit)
    {
        $this->authorize('update', $visit);

        $request->validate([
            'document' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'], // Max 5MB
            'caption'  => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('document');
        $path = $file->store("visits/{$visit->id}", 'public');

        $document = $visit->documents()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'file_size' => $file->getSize(),
            'caption'   => $request->caption,
        ]);

        $this->auditService->logCreate($document);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'document' => [
                    'id' => $document->id,
                    'file_path' => asset('storage/' . $document->file_path),
                    'caption' => $document->caption ?: $document->file_name,
                    'size' => $document->fileSizeLabel(),
                    'is_image' => $document->isImage(),
                    'delete_url' => route('visits.documents.destroy', [$visit, $document])
                ]
            ]);
        }

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Foto/Dokumen kunjungan berhasil diunggah.');
    }

    public function deleteDocument(Visit $visit, \App\Models\VisitDocument $document)
    {
        $this->authorize('update', $visit);

        \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
        $document->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Dokumen berhasil dihapus.');
    }
}
