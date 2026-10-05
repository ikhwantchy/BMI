<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--keep=14 : Jumlah hari retensi backup}';
    protected $description = 'Cadangkan database dan file penting Koperasi Syariah BMI';

    public function handle(): int
    {
        $this->info('Memulai proses backup database Kopsyah BMI...');

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = now()->format('Ymd_His');
        $driver = config('database.default');

        if ($driver === 'sqlite') {
            $sourceDb = config('database.connections.sqlite.database');
            $targetFile = "{$backupDir}/kopsyah_bmi_sqlite_{$timestamp}.sqlite";
            if (File::exists($sourceDb)) {
                File::copy($sourceDb, $targetFile);
                $this->info("Backup SQLite berhasil disimpan ke: {$targetFile}");
            }
        } elseif ($driver === 'mysql') {
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host', '127.0.0.1');
            $dbPort = config('database.connections.mysql.port', '3306');

            $targetSql = "{$backupDir}/kopsyah_bmi_mysql_{$timestamp}.sql";
            $targetGz = "{$targetSql}.gz";

            $passwordFlag = $dbPass ? "-p" . escapeshellarg($dbPass) : '';
            $command = sprintf(
                'mysqldump -h %s -P %s -u %s %s %s > %s',
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                $passwordFlag,
                escapeshellarg($dbName),
                escapeshellarg($targetSql)
            );

            exec($command, $output, $returnVar);

            if ($returnVar === 0 && File::exists($targetSql)) {
                // Compress sql file
                if (function_exists('gzopen')) {
                    $gz = gzopen($targetGz, 'w9');
                    $fp = fopen($targetSql, 'r');
                    while (!feof($fp)) {
                        gzwrite($gz, fread($fp, 1024 * 512));
                    }
                    fclose($fp);
                    gzclose($gz);
                    File::delete($targetSql);
                    $this->info("Backup MySQL berhasil dikompresi: {$targetGz}");
                } else {
                    $this->info("Backup MySQL berhasil disimpan: {$targetSql}");
                }
            } else {
                $this->error("Gagal menjalankan mysqldump (exit code: {$returnVar}).");
                return 1;
            }
        }

        // Cleanup old backups
        $keepDays = (int) $this->option('keep');
        $expiryTime = now()->subDays($keepDays)->timestamp;
        $files = File::files($backupDir);
        $deletedCount = 0;

        foreach ($files as $file) {
            if ($file->getMTime() < $expiryTime) {
                File::delete($file->getPathname());
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->info("Membersihkan {$deletedCount} file backup lama (> {$keepDays} hari).");
        }

        // Audit Log
        try {
            AuditLog::create([
                'user_id'     => null,
                'action'      => 'backup_completed',
                'entity_type' => 'Database',
                'entity_id'   => null,
                'context'     => "Cadangan otomatis berhasil dibuat pada {$timestamp}",
            ]);
        } catch (\Throwable $e) {
            // Ignore audit log error in CLI
        }

        $this->info('✅ Backup selesai dengan sukses.');
        return 0;
    }
}
