<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Member;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index()
    {
        $this->authorizeAccess();

        return view('import.index');
    }

    public function template(string $type)
    {
        $this->authorizeAccess();

        if ($type === 'members') {
            $filename = 'template_impor_anggota_bmi.csv';
            $headers = ['nomor_anggota', 'nama_lengkap', 'alamat', 'telepon', 'status_keanggotaan', 'catatan'];
            $sample = ['BMI-2026-901', 'Fulan bin Fulan', 'Jl. Sukajadi No. 10, Tangerang', '08123456789', 'active', 'Anggota pembiayaan produktif'];
        } else {
            $filename = 'template_impor_usaha_bmi.csv';
            $headers = ['nomor_anggota', 'nama_usaha', 'jenis_usaha', 'alamat_usaha', 'usia_usaha_bulan', 'modal_awal', 'produk_layanan'];
            $sample = ['BMI-2024-001', 'Warung Berkah Jaya', 'Perdagangan / Kelontong', 'Pasar Curug Kios No. 5', '24', '10000000', 'Sembako dan kebutuhan dapur'];
        }

        return response()->streamDownload(function () use ($headers, $sample) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, $headers);
            fputcsv($handle, $sample);
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function preview(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'type' => ['required', 'in:members,businesses'],
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'], // Max 2MB
        ]);

        $type = $request->type;
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        // Check BOM
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle, 1000, ',');
        if (!$header) {
            return back()->with('error', 'File CSV kosong atau tidak terbaca.');
        }

        $cleanHeader = array_map(fn($h) => strtolower(trim($h)), $header);

        $rows = [];
        $errors = [];
        $validRows = [];
        $rowNum = 1;

        $user = Auth::user();
        $branchId = $user->branch_id;

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNum++;
            if (count($data) < count($cleanHeader)) {
                continue;
            }

            $row = array_combine($cleanHeader, array_slice($data, 0, count($cleanHeader)));
            $rowErrors = [];

            if ($type === 'members') {
                $no = trim($row['nomor_anggota'] ?? '');
                $nama = trim($row['nama_lengkap'] ?? '');
                $alamat = trim($row['alamat'] ?? '');

                if (empty($no)) $rowErrors[] = 'Nomor anggota wajib diisi';
                elseif (Member::where('member_number', $no)->exists()) $rowErrors[] = "Nomor anggota {$no} sudah terdaftar";

                if (empty($nama)) $rowErrors[] = 'Nama lengkap wajib diisi';
                if (empty($alamat)) $rowErrors[] = 'Alamat wajib diisi';

                $parsedRow = [
                    'member_number'     => $no,
                    'full_name'         => $nama,
                    'address'           => $alamat,
                    'phone'             => trim($row['telepon'] ?? ''),
                    'membership_status' => 'active',
                    'notes'             => trim($row['catatan'] ?? ''),
                    'branch_id'         => $branchId,
                ];
            } else {
                $memberNo = trim($row['nomor_anggota'] ?? '');
                $namaUsaha = trim($row['nama_usaha'] ?? '');
                $jenisUsaha = trim($row['jenis_usaha'] ?? '');

                $member = Member::where('member_number', $memberNo)->first();
                if (!$member) {
                    $rowErrors[] = "Nomor anggota {$memberNo} tidak ditemukan di sistem";
                }

                if (empty($namaUsaha)) $rowErrors[] = 'Nama usaha wajib diisi';
                if (empty($jenisUsaha)) $rowErrors[] = 'Jenis usaha wajib diisi';

                $parsedRow = [
                    'member_id'           => $member?->id,
                    'member_no'           => $memberNo,
                    'member_name'         => $member?->full_name,
                    'name'                => $namaUsaha,
                    'business_type'       => $jenisUsaha,
                    'address'             => trim($row['alamat_usaha'] ?? ''),
                    'business_age_months' => (int) ($row['usia_usaha_bulan'] ?? 0),
                    'initial_capital'     => (int) ($row['modal_awal'] ?? 0),
                    'products_services'   => trim($row['produk_layanan'] ?? ''),
                    'status'              => 'active',
                    'branch_id'           => $branchId,
                ];
            }

            $rows[] = [
                'row_number' => $rowNum,
                'data'       => $parsedRow,
                'errors'     => $rowErrors,
                'is_valid'   => empty($rowErrors),
            ];

            if (empty($rowErrors)) {
                $validRows[] = $parsedRow;
            }
        }
        fclose($handle);

        $importToken = Str::uuid()->toString();
        Session::put("import_{$importToken}", [
            'type'  => $type,
            'rows'  => $validRows,
            'total' => count($rows),
            'valid' => count($validRows),
        ]);

        return view('import.preview', compact('rows', 'type', 'importToken'));
    }

    public function confirm(Request $request)
    {
        $this->authorizeAccess();

        $token = $request->import_token;
        $sessionData = Session::get("import_{$token}");

        if (!$sessionData || empty($sessionData['rows'])) {
            return redirect()->route('import.index')->with('error', 'Sesi pratinjau impor telah kedaluwarsa atau tidak ada data valid.');
        }

        $type = $sessionData['type'];
        $rows = $sessionData['rows'];
        $importedCount = 0;

        DB::transaction(function () use ($type, $rows, &$importedCount) {
            foreach ($rows as $row) {
                if ($type === 'members') {
                    Member::create($row);
                } else {
                    unset($row['member_no'], $row['member_name']);
                    Business::create($row);
                }
                $importedCount++;
            }
        });

        Session::forget("import_{$token}");

        $entityName = $type === 'members' ? 'Anggota' : 'Usaha';
        $this->auditService->log('import', $type === 'members' ? Member::class : Business::class, null, [], [], "Berhasil mengimpor massal {$importedCount} data {$entityName}");

        $targetRoute = $type === 'members' ? 'members.index' : 'businesses.index';
        return redirect()->route($targetRoute)->with('success', "Sukses mengimpor {$importedCount} data {$entityName} secara transaksional.");
    }

    private function authorizeAccess(): void
    {
        $user = Auth::user();
        $allowed = ['system_admin', 'manajer', 'pengurus'];
        if (!in_array($user->role, $allowed) && !$user->hasAnyRole($allowed)) {
            abort(403, 'Akses ditolak. Fitur impor hanya untuk Administrator, Manajer, dan Pengurus.');
        }
    }
}
