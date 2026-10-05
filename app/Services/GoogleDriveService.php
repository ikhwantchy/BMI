<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected string $credentialsPath;
    protected ?string $folderId;

    public function __construct()
    {
        $this->credentialsPath = storage_path('app/credentials/google-drive-service-account.json');
        $this->folderId = config('services.google_drive.folder_id') ?? env('GOOGLE_DRIVE_FOLDER_ID');
    }

    /**
     * Check if Google Drive is configured and credentials exist.
     */
    public function isConfigured(): bool
    {
        return file_exists($this->credentialsPath) && !empty($this->folderId);
    }

    /**
     * Get folder ID.
     */
    public function getFolderId(): ?string
    {
        return $this->folderId;
    }

    /**
     * Get configuration info (safe for display, masking secrets).
     */
    public function getConfigInfo(): array
    {
        $info = [
            'configured'   => $this->isConfigured(),
            'has_key_file' => file_exists($this->credentialsPath),
            'client_email' => null,
            'folder_id'    => $this->folderId,
            'project_id'   => null,
        ];

        if (file_exists($this->credentialsPath)) {
            $json = json_decode(file_get_contents($this->credentialsPath), true);
            if (is_array($json)) {
                $info['client_email'] = $json['client_email'] ?? null;
                $info['project_id']   = $json['project_id'] ?? null;
            }
        }

        return $info;
    }

    /**
     * Save uploaded credentials file and folder ID.
     */
    public function saveCredentials(string $jsonContent, ?string $folderId = null): bool
    {
        $dir = dirname($this->credentialsPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Validate JSON
        $data = json_decode($jsonContent, true);
        if (!is_array($data) || empty($data['client_email']) || empty($data['private_key'])) {
            throw new \InvalidArgumentException('Format file JSON Service Account tidak valid. Harus mengandung client_email dan private_key.');
        }

        file_put_contents($this->credentialsPath, $jsonContent);

        if ($folderId) {
            $this->updateEnvFolderId($folderId);
            $this->folderId = $folderId;
        }

        // Clear cached access token
        Cache::forget('google_drive_access_token');

        return true;
    }

    /**
     * Test connection to Google Drive API.
     */
    public function testConnection(): array
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return ['success' => false, 'message' => 'Gagal mendapatkan Access Token dari Google OAuth.'];
            }

            // Verify access to the folder or about info
            $response = Http::withToken($token)
                ->get("https://www.googleapis.com/drive/v3/files/{$this->folderId}", [
                    'fields' => 'id,name,mimeType,capabilities',
                    'supportsAllDrives' => 'true',
                ]);

            if ($response->successful()) {
                $folderData = $response->json();
                return [
                    'success'     => true,
                    'folder_name' => $folderData['name'] ?? 'Folder Terhubung',
                    'can_add'     => $folderData['capabilities']['canAddChildren'] ?? true,
                    'message'     => 'Koneksi ke Google Drive berhasil! Folder ditemukan.',
                ];
            }

            // If folder check fails, test general drive access
            $aboutResponse = Http::withToken($token)
                ->get('https://www.googleapis.com/drive/v3/about', ['fields' => 'user']);

            if ($aboutResponse->successful()) {
                return [
                    'success' => false,
                    'message' => 'Google Drive API terhubung, tetapi Folder ID tidak dapat diakses. Pastikan folder sudah di-share ke email Service Account sebagai "Editor".',
                ];
            }

            return [
                'success' => false,
                'message' => 'Error Google Drive API: ' . ($response->json()['error']['message'] ?? $response->body()),
            ];
        } catch (\Throwable $e) {
            Log::error('GoogleDriveService testConnection failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Koneksi gagal: ' . $e->getMessage()];
        }
    }

    /**
     * Upload a local file to Google Drive.
     */
    public function uploadFile(string $filePath, ?string $customFilename = null): array
    {
        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => "File tidak ditemukan di path: {$filePath}"];
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Gagal autentikasi ke Google Drive API.'];
        }

        $filename = $customFilename ?: basename($filePath);
        $fileSize = filesize($filePath);
        $fileStream = fopen($filePath, 'r');

        $metadata = [
            'name' => $filename,
        ];
        if (!empty($this->folderId)) {
            $metadata['parents'] = [$this->folderId];
        }

        try {
            // Upload using multipart
            $response = Http::withToken($token)
                ->attach('metadata', json_encode($metadata), 'metadata.json', ['Content-Type' => 'application/json; charset=UTF-8'])
                ->attach('file', $fileStream, $filename, ['Content-Type' => 'application/octet-stream'])
                ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true');

            if (is_resource($fileStream)) {
                fclose($fileStream);
            }

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success'      => true,
                    'file_id'      => $data['id'] ?? null,
                    'file_name'    => $data['name'] ?? $filename,
                    'size'         => $fileSize,
                    'uploaded_at'  => now(),
                ];
            }

            $errorMsg = $response->json()['error']['message'] ?? $response->body();
            return ['success' => false, 'message' => "Gagal upload ke Google Drive: {$errorMsg}"];
        } catch (\Throwable $e) {
            if (is_resource($fileStream)) {
                fclose($fileStream);
            }
            Log::error('GoogleDriveService uploadFile error: ' . $e->getMessage());
            return ['success' => false, 'message' => "Terjadi kesalahan upload: " . $e->getMessage()];
        }
    }

    /**
     * List files in the configured backup folder on Google Drive.
     */
    public function listDriveFiles(int $limit = 20): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            $token = $this->getAccessToken();
            if (!$token) return [];

            $q = "'{$this->folderId}' in parents and trashed = false";
            $response = Http::withToken($token)
                ->get('https://www.googleapis.com/drive/v3/files', [
                    'q'                 => $q,
                    'pageSize'          => $limit,
                    'orderBy'           => 'createdTime desc',
                    'fields'            => 'files(id, name, size, createdTime, webViewLink)',
                    'supportsAllDrives' => 'true',
                ]);

            if ($response->successful()) {
                $files = $response->json()['files'] ?? [];
                return array_map(function ($f) {
                    return [
                        'id'           => $f['id'],
                        'name'         => $f['name'],
                        'size'         => (int) ($f['size'] ?? 0),
                        'created_at'   => isset($f['createdTime']) ? \Carbon\Carbon::parse($f['createdTime']) : null,
                        'web_link'     => $f['webViewLink'] ?? null,
                    ];
                }, $files);
            }
        } catch (\Throwable $e) {
            Log::warning('listDriveFiles failed: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Get OAuth Access Token using Google Service Account JWT.
     */
    protected function getAccessToken(): ?string
    {
        return Cache::remember('google_drive_access_token', 3300, function () {
            if (!file_exists($this->credentialsPath)) {
                return null;
            }

            $keyData = json_decode(file_get_contents($this->credentialsPath), true);
            if (!is_array($keyData) || empty($keyData['client_email']) || empty($keyData['private_key'])) {
                return null;
            }

            $now = time();
            $header = ['alg' => 'RS256', 'typ' => 'JWT'];
            $claim = [
                'iss'   => $keyData['client_email'],
                'scope' => 'https://www.googleapis.com/auth/drive',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'exp'   => $now + 3600,
                'iat'   => $now,
            ];

            $encodedHeader = $this->base64UrlEncode(json_encode($header));
            $encodedClaim  = $this->base64UrlEncode(json_encode($claim));
            $toSign        = "{$encodedHeader}.{$encodedClaim}";

            $signature = '';
            $privateKey = openssl_pkey_get_private($keyData['private_key']);
            if (!$privateKey) {
                Log::error('GoogleDriveService: Invalid private key format');
                return null;
            }

            if (!openssl_sign($toSign, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                Log::error('GoogleDriveService: openssl_sign failed');
                return null;
            }

            $encodedSignature = $this->base64UrlEncode($signature);
            $jwt = "{$toSign}.{$encodedSignature}";

            // Request access token from Google
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'] ?? null;
            }

            Log::error('GoogleDriveService OAuth failed: ' . $response->body());
            return null;
        });
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Update GOOGLE_DRIVE_FOLDER_ID in .env
     */
    protected function updateEnvFolderId(string $folderId): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) return;

        $content = file_get_contents($envPath);
        if (str_contains($content, 'GOOGLE_DRIVE_FOLDER_ID=')) {
            $content = preg_replace('/^GOOGLE_DRIVE_FOLDER_ID=.*/m', "GOOGLE_DRIVE_FOLDER_ID={$folderId}", $content);
        } else {
            $content .= "\nGOOGLE_DRIVE_FOLDER_ID={$folderId}\n";
        }
        file_put_contents($envPath, $content);
    }
}
