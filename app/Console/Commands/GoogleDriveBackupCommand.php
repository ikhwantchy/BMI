<?php

namespace App\Console\Commands;

use App\Services\AuditService;
use App\Services\GoogleDriveService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GoogleDriveBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run-gdrive {--upload-only : Hanya upload file backup terbaru yang ada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Buat backup database otomatis dan unggah ke Google Drive';

    /**
     * Execute the console command.
     */
    public function handle(GoogleDriveService $gdrive, AuditService $audit)
    {
        $this->info('Memulai proses backup database...');

        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $uploadOnly = $this->option('upload-only');
        $outputPath = null;
        $filename   = null;

        if (!$uploadOnly) {
            $dbDriver = config('database.default');
            $timestamp = now()->format('Ymd_His');

            if ($dbDriver === 'sqlite') {
                $source = config('database.connections.sqlite.database');
                $filename = "backup_kopsyah_sqlite_{$timestamp}.sqlite";
                $outputPath = $backupDir . DIRECTORY_SEPARATOR . $filename;
                if (!file_exists($source)) {
                    $this->error("Database SQLite tidak ditemukan di: {$source}");
                    return 1;
                }
                copy($source, $outputPath);
            } elseif ($dbDriver === 'mysql' || $dbDriver === 'mariadb') {
                $filename = "backup_kopsyah_{$timestamp}.sql";
                $outputPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

                $dbHost     = config("database.connections.{$dbDriver}.host", '127.0.0.1');
                $dbPort     = config("database.connections.{$dbDriver}.port", 3306);
                $dbName     = config("database.connections.{$dbDriver}.database");
                $dbUser     = config("database.connections.{$dbDriver}.username");
                $dbPassword = config("database.connections.{$dbDriver}.password");

                $cmd = sprintf(
                    'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --quick --lock-tables=false %s > %s 2>&1',
                    escapeshellarg($dbHost),
                    escapeshellarg($dbPort),
                    escapeshellarg($dbUser),
                    escapeshellarg($dbPassword),
                    escapeshellarg($dbName),
                    escapeshellarg($outputPath)
                );

                exec($cmd, $output, $returnCode);

                if ($returnCode !== 0 || !file_exists($outputPath) || filesize($outputPath) < 50) {
                    if (file_exists($outputPath)) unlink($outputPath);
                    $this->error('mysqldump gagal: ' . implode(' ', $output));
                    return 1;
                }
            } else {
                $this->error("Driver database '{$dbDriver}' tidak didukung untuk backup otomatis.");
                return 1;
            }

            $sizeKb = number_format(filesize($outputPath) / 1024, 1);
            $this->info("Backup lokal berhasil dibuat: {$filename} ({$sizeKb} KB)");
        } else {
            // Find latest file in backups dir
            $files = glob($backupDir . '/*.*');
            if (empty($files)) {
                $this->warn('Tidak ada file backup untuk diunggah.');
                return 0;
            }
            usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
            $outputPath = $files[0];
            $filename   = basename($outputPath);
            $this->info("Menggunakan file backup terbaru: {$filename}");
        }

        // Upload to Google Drive if configured
        if ($gdrive->isConfigured()) {
            $this->info('Mengunggah backup ke Google Drive...');
            $result = $gdrive->uploadFile($outputPath, $filename);

            if ($result['success']) {
                $this->info("Berhasil diunggah ke Google Drive! File ID: {$result['file_id']}");
                Log::info("Backup otomatis Google Drive berhasil: {$filename}");
                $audit->log('create', 'BackupFile', null, [], [], "Backup otomatis Google Drive berhasil: {$filename} (ID: {$result['file_id']})");
            } else {
                $this->warn("Gagal unggah ke Google Drive: {$result['message']}");
                Log::warning("Backup lokal berhasil tetapi gagal ke Google Drive: {$result['message']}");
            }
        } else {
            $this->warn('Google Drive belum dikonfigurasi. Backup tersimpan di lokal saja.');
        }

        // Clean up old local backups (> 30 days)
        $this->cleanupOldBackups($backupDir, 30);

        return 0;
    }

    protected function cleanupOldBackups(string $dir, int $daysToKeep): void
    {
        $threshold = now()->subDays($daysToKeep)->timestamp;
        $files = glob($dir . '/*.*');
        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $threshold) {
                unlink($file);
                $this->info("Membersihkan file backup usang: " . basename($file));
            }
        }
    }
}
