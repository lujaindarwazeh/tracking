<?php
namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log ;
use Illuminate\Support\Facades\Mail;
use App\Mail\BackupFailureAlert;


class GenerateIncrementalSqlBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public function handle(): void
    {

        if(DB::table('cache')->where('key','backup_job_failures')){
            DB::table('cache')->where('key','backup_job_failures')->delete();
        }
        
        $now = Carbon::now();
        //$lastBackupTime = cache('last_backup_time', now()->subYears(1));
        $lastBackupTime = DB::table('cache')->where('key', 'last_backup_time')->value('value') ?? now()->subYears(1);


        

        DB::table('cache')->updateOrInsert(
            ['key' => 'last_backup_time'],
            ['value' => $lastBackupTime]
        

        );

        $lastBackupTime = Carbon::parse($lastBackupTime);
        Log::info("Last backup time: " . $lastBackupTime->toDateTimeString());

        $database = config('database.connections.mysql.database');

        
        $tables = DB::select('SHOW TABLES');
        $dbKey = 'Tables_in_' . $database;
        $tableNames = array_map(fn($t) => $t->$dbKey, $tables);

        $date = $now->format('Y-m-d');
        $time = $now->format('H-i-s');

        $basePath = storage_path("app/backups");
        $folderPath = "{$basePath}/{$date}";  
        $fullFile = "{$basePath}/full.sql";   

        


        if (!File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true);
        }

        $fullFile = "{$basePath}/full.sql";

        
        if (!File::exists($fullFile)) {
            $this->createFullBackup($fullFile, $tableNames);
            info("Full backup created: {$fullFile}");
            $lastBackupTime = Carbon::now();
            Log::info("Setting last backup time to: " . $lastBackupTime->toDateTimeString());
            //cache(['last_backup_time' => $lastBackupTime->toDateTimeString()]);

            DB::table('cache')->updateOrInsert(
                ['key' => 'last_backup_time'],
                ['value' => $lastBackupTime]
              
            );


            return;
        }


        
        $changes = $this->getChanges($tableNames, $lastBackupTime);

        if (empty($changes)) {
            info('No changes detected. Skipping incremental backup.');
            return;
        }

        if(!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

       
        $incFile = "{$folderPath}/inc-{$time}.sql";
        $this->createIncrementalBackup($incFile, $changes);

        
       // cache(['last_backup_time' => Carbon::now()->toDateTimeString()]);

        DB::table('cache')->updateOrInsert(
            ['key' => 'last_backup_time'],
            ['value' => Carbon::now()->toDateTimeString()]
            
        );


        Log::info("Updated last backup time to: " . Carbon::now()->toDateTimeString());

        info("Incremental backup created: {$incFile}");
    }


    public function failed(\Throwable $exception)
    {
        $key = 'backup_job_failures';
       
        $failures = DB::table('cache')->where('key', $key)->value('value') ?? 1;

        DB::table('cache')->updateOrInsert(
            ['key' => $key],
            ['value' => $failures]
           
        );

        Log::error("GenerateIncrementalSqlBackup job failed #{$failures}: " . $exception->getMessage());

        if ($failures >= 3) {
            
            
            try {
                Mail::to('lujain.darwazeh123@gmailcom')->send( new BackupFailureAlert($exception, $failures));
                Log::info("Backup failure alert email sent after {$failures} failures.");

                
               

                DB::table('cache')->where('key', $key)->delete();


                $failures = 0;
            } catch (\Exception $mailEx) {
                Log::error("Failed to send backup alert email: " . $mailEx->getMessage());
            }
        }

        else {
           $failures+=1;
            Log::info("Backup job failure count: {$failures}. No email sent yet.");
        }
    }




    private function createFullBackup(string $filepath, array $tables): void
    {
        $sql = "-- FULL BACKUP\n-- Date: " . now()->toDateTimeString() . "\nSET FOREIGN_KEY_CHECKS=0;\n\n";

        Log::info("Creating full backup at time " . now()->toDateTimeString());

        foreach ($tables as $table) {
            $createTable = DB::select("SHOW CREATE TABLE {$table}");
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

            $primaryKey = $this->getPrimaryKey($table);

            if ($primaryKey) {
                DB::table($table)->orderBy($primaryKey)->chunkById(100, function ($rows) use (&$sql, $table) {
                    foreach ($rows as $row) {
                        $rowArray = (array)$row;
                        $columns = implode(', ', array_keys($rowArray));
                        $values = implode(', ', array_map(fn($v) => is_null($v) ? 'NULL' : "'" . addslashes($v) . "'", $rowArray));
                        $sql .= "INSERT INTO {$table} ({$columns}) VALUES ({$values});\n";
                    }
                }, $primaryKey);
            } else {
                
                DB::table($table)->chunk(100, function ($rows) use (&$sql, $table) {
                    foreach ($rows as $row) {
                        $rowArray = (array)$row;
                        $columns = implode(', ', array_keys($rowArray));
                        $values = implode(', ', array_map(fn($v) => is_null($v) ? 'NULL' : "'" . addslashes($v) . "'", $rowArray));
                        $sql .= "INSERT INTO {$table} ({$columns}) VALUES ({$values});\n";
                    }
                });
            }

            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        File::put($filepath, $sql);
    }



    private function getChanges(array $tables, $since): array
    {
        $changes = [];

        foreach ($tables as $table) {
            try {
                $columns = DB::select("SHOW COLUMNS FROM {$table}");
                $hasTimestamps = collect($columns)->contains(fn($col) =>
                    in_array($col->Field, ['created_at', 'updated_at', 'deleted_at'])
                );
                if (!$hasTimestamps) continue;

                $primaryKey = $this->getPrimaryKey($table);

                if ($primaryKey) {
                    $changes[$table] = collect();

                    DB::table($table)
                        ->orderBy($primaryKey)
                        ->where(function ($query) use ($since) {
                            $query->where('updated_at', '>', $since)
                                ->orWhere('created_at', '>', $since)
                                ->orWhere('deleted_at', '>', $since);
                        })
                        ->chunkById(100, function ($rows) use (&$changes, $table) {
                            $changes[$table] = $changes[$table]->merge($rows);
                        }, $primaryKey);
                } else {
                 
                    $changes[$table] = collect();

                    DB::table($table)
                        ->where(function ($query) use ($since) {
                            $query->where('updated_at', '>', $since)
                                ->orWhere('created_at', '>', $since)
                                ->orWhere('deleted_at', '>', $since);
                        })
                        ->chunk(100, function ($rows) use (&$changes, $table) {
                            $changes[$table] = $changes[$table]->merge($rows);
                        });
                }

                if ($changes[$table]->isEmpty()) {
                    unset($changes[$table]);
                }

            } catch (\Exception $e) {
                continue;
            }
        }

        return $changes;

    }


    private function createIncrementalBackup(string $filepath, array $changes): void
    {
        $now = Carbon::now();
        
        $maxSize = 5 * 1024 ; 
        $dir = dirname($filepath);
        $fileIndex = 1;

        $baseName = pathinfo($filepath, PATHINFO_FILENAME);
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        $currentFile = "{$dir}/{$baseName}-part{$fileIndex}.{$ext}";

        $totalRows = 0;

       
        $sql = "-- INCREMENTAL BACKUP\n";
        $sql .= "-- Date: {$now}\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($changes as $table => $rows) {
            if ($rows->isEmpty()) continue;

            $totalRows += $rows->count();
            $primaryKey = $this->getPrimaryKey($table);

            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $columns = array_keys($rowArray);

                $values = array_map(function ($v) {
                    return is_null($v) ? 'NULL' : "'" . addslashes($v) . "'";
                }, $rowArray);

                $sql .= "INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";

                if ($primaryKey) {
                    $updates = [];
                    foreach ($columns as $col) {
                        if ($col !== $primaryKey) {
                            $updates[] = "`{$col}` = VALUES(`{$col}`)";
                        }
                    }
                    if (!empty($updates)) {
                        $sql .= " ON DUPLICATE KEY UPDATE " . implode(', ', $updates);
                    }
                }

                $sql .= ";\n";

          
                if (strlen($sql) >= $maxSize) {
                    $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
                    File::put($currentFile, $sql);
                    Log::info("Incremental backup part created: {$currentFile}");

                    $fileIndex++;
                    $currentFile = "{$dir}/{$baseName}-part{$fileIndex}.{$ext}";

                    $sql = "-- CONTINUED INCREMENTAL BACKUP\n";
                    $sql .= "-- Date: {$now}\n\n";
                    $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
                }
            }

            $sql .= "\n";
        }

        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        if ($totalRows > 0 && strlen($sql) > 0) {
            
            File::put($currentFile, $sql);

            Log::info("Final incremental backup created: {$currentFile} ({$totalRows} rows)");
        } else {
            Log::info("No changed rows detected — no incremental backup created.");
        }
   }



    private function getPrimaryKey(string $table): ?string
    {
        try {
            $keys = DB::select("SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'");
            return $keys[0]->Column_name ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}