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
        $businesses = Business::with('member')->active()->orderBy('name')->get();
        $selectedBusiness = $request->business_id ? Business::with('member')->find($request->business_id) : null;

        return view('visits.create', compact('businesses', 'selectedBusiness'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_id'       => ['required', 'exists:businesses,id'],
            'visit_date'        => ['required', 'date'],
            'evaluation_period' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'field_notes'       => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['officer_id'] = $request->user()->id;
        $validated['status']     = VisitStatus::Scheduled->value;

        $visit = Visit::create($validated);
        $this->auditService->logCreate($visit);

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Jadwal kunjungan berhasil dibuat.');
    }

    public function show(Visit $visit)
    {
        $visit->load(['business.member', 'officer', 'documents', 'evaluation.details.parameter']);

        return view('visits.show', compact('visit'));
    }

    public function edit(Visit $visit)
    {
        $businesses = Business::with('member')->active()->orderBy('name')->get();

        return view('visits.edit', compact('visit', 'businesses'));
    }

    public function update(Request $request, Visit $visit)
    {
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

    public function complete(Request $request, Visit $visit)
    {
        abort_if($visit->status === VisitStatus::Completed, 403, 'Kunjungan sudah selesai.');

        $visit->update(['status' => VisitStatus::Completed]);
        $this->auditService->log('complete', Visit::class, $visit->id);

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Status kunjungan diperbarui menjadi Selesai.');
    }

    public function uploadDocument(Request $request, Visit $visit)
    {
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

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Foto/Dokumen kunjungan berhasil diunggah.');
    }

    public function deleteDocument(Visit $visit, \App\Models\VisitDocument $document)
    {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Dokumen berhasil dihapus.');
    }
}
