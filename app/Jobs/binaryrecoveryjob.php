<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class binaryrecoveryjob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
     public function handle(): void
    {
        Log::info("🕐 Backup job started at " . Carbon::now()->toDateTimeString());

        $db     = config('database.connections.mysql.database');
        $user   = config('database.connections.mysql.username');
        $pass   = config('database.connections.mysql.password');
        $host   = config('database.connections.mysql.host');
        $bindata = config('database.connections.mysql.binlog_path', 'C:/xampp/mysql/data');
        $mysqlbinlog = 'C:/xampp/mysql/bin/mysqlbinlog.exe';

        $basePath = storage_path("app/backups3");
        File::ensureDirectoryExists($basePath);

        $fullFile = $basePath . "/full.dump";
        $now = Carbon::now();
        $incrementalFile = $basePath . "/inc-" . $now->format('Y-m-d-H-i-s') . ".sql";

        $lastPosition = DB::table('cache')->where('key', 'last_binlog_position')->value('value');
        $current = DB::selectOne('SHOW MASTER STATUS');

        if (!$current) {
            Log::error("❌ Binary logging is not enabled!");
            return;
        }

        if (!File::exists($fullFile)) {
            Log::info("📦 No full backup found. Creating one...");

            $cmd = "mysqldump -h {$host} -u {$user} -p\"{$pass}\" {$db} > \"{$fullFile}\"";
            exec($cmd, $output, $code);

            if ($code !== 0) {
                Log::error("❌ Full backup failed!");
                return;
            }

            DB::table('cache')->updateOrInsert(
                ['key' => 'last_binlog_position'],
                ['value' => json_encode([
                    'file' => $current->File,
                    'position' => $current->Position
                ])]
            );

            Log::info("✅ Full backup created: {$fullFile}");
            return;
        }

        $currentFile = $current->File;
        $currentPos = $current->Position;

        if (!$lastPosition) {
            Log::warning("⚠️ No previous binlog position found.");
            return;
        }

        $lastPosition = json_decode($lastPosition, true);

        // if ($lastPosition['file'] === $currentFile && $lastPosition['position'] === $currentPos) {
        //     Log::info("✅ No new changes detected.");
        //     return;
        // }




        $positionDiff = $currentPos - $lastPosition['position'];

        if ($lastPosition['file'] === $currentFile && $positionDiff < 300) {
            Log::info("⏩ Binlog position advanced by only {$positionDiff} bytes — skipping backup.");
            return;
        }

        // $cmd = "\"{$mysqlbinlog}\" --database={$db} --start-position={$lastPosition['position']} --stop-position={$currentPos} \"{$bindata}/{$currentFile}\" > \"{$incrementalFile}\"";
        // exec($cmd, $output, $code);

        // if ($code !== 0) {
        //     Log::error("❌ Failed to extract binary log!");
        //     return;
        // }

        // $allowedTables = ['company','companyevent','companyusers','event','userevent','zone'];
        // $allowedOps = ['insert', 'update'];

        // $lines = file($incrementalFile);
        // $filtered = array_filter($lines, function ($line) use ($allowedTables, $allowedOps) {
        //     $lineLower = strtolower($line);
        //     foreach ($allowedOps as $op) {
        //         if (str_contains($lineLower, $op)) {
        //             foreach ($allowedTables as $table) {
        //                 if (str_contains($lineLower, "`$table`")) return true;
        //             }
        //         }
        //     }
        //     return false;
        // });

        // if (empty($filtered)) {
        //     File::delete($incrementalFile);
        //     Log::info("⚙️ No relevant changes found — incremental file deleted.");
        // } else {
        //     file_put_contents($incrementalFile, implode('', $filtered));
        //     Log::info("✅ Incremental backup created: {$incrementalFile}");
        // }

        $binlogFiles = DB::select('SHOW BINARY LOGS');
        $startIndex = null;
        $endIndex = null;

        
        foreach ($binlogFiles as $i => $log) {
            if ($log->Log_name === $lastPosition['file']) $startIndex = $i;
            if ($log->Log_name === $currentFile) $endIndex = $i;
        }

        if ($startIndex === null || $endIndex === null || $startIndex > $endIndex) {
            Log::error("❌ Invalid binlog range: {$lastPosition['file']} to {$currentFile}");
            return;
        }

        $tempFiles = [];

        for ($i = $startIndex; $i <= $endIndex; $i++) {
            $file = $binlogFiles[$i]->Log_name;
            $tempFile = $basePath . "/temp-" . $file . ".sql";
            $cmd = "\"{$mysqlbinlog}\" --database={$db}";

            if ($i === $startIndex) {
                $cmd .= " --start-position={$lastPosition['position']}";
            }
            if ($i === $endIndex) {
                $cmd .= " --stop-position={$currentPos}";
            }

            $cmd .= " \"{$bindata}/{$file}\" > \"{$tempFile}\"";
            exec($cmd, $output, $code);

            if ($code !== 0) {
                Log::error("❌ Failed to extract {$file}");
                return;
            }

            $tempFiles[] = $tempFile;
        }

       
        $allowedTables = ['company','companyevent','companyusers','event','userevent','zone'];
        $allowedOps = ['insert', 'update'];
        $filteredLines = [];

       
       $pendingInsertId = null;

        foreach ($tempFiles as $file) {
            $lines = file($file);
            foreach ($lines as $line) {
                $lineLower = strtolower($line);

                // Capture INSERT_ID
                if (preg_match('/SET INSERT_ID=(\d+)/i', $line, $matches)) {
                    $pendingInsertId = $matches[1];
                    continue;
                }

                // Match INSERT or UPDATE
                foreach ($allowedOps as $op) {
                    if (str_contains($lineLower, $op)) {
                        foreach ($allowedTables as $table) {
                            if (str_contains($lineLower, "`$table`")) {
                                if ($pendingInsertId !== null) {
                                    $filteredLines[] = "-- INSERT_ID={$pendingInsertId}\n";
                                    $pendingInsertId = null;
                                }
                                $filteredLines[] = $line;
                                break 2;
                            }
                        }
                    }
                }
            }
            File::delete($file);
        }


        if (empty($filteredLines)) {
            Log::info("⚙️ No relevant changes found — no incremental file created.");
        } else {
            file_put_contents($incrementalFile, implode('', $filteredLines));
            Log::info("✅ Incremental backup created: {$incrementalFile}");
        }


        DB::table('cache')->updateOrInsert(
            ['key' => 'last_binlog_position'],
            ['value' => json_encode([
                'file' => $currentFile,
                'position' => $currentPos
            ])]
        );

        Log::info("📊 Binlog updated from {$lastPosition['file']}:{$lastPosition['position']} to {$currentFile}:{$currentPos}");
    }
}
