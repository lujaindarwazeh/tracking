<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Mail\BackupFailureAlert;
use Carbon\Carbon;

class mysqldumpjob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $db     = config('database.connections.mysql.database');
        $user   = config('database.connections.mysql.username');
        $pass   = config('database.connections.mysql.password');
        $host   = config('database.connections.mysql.host');

        $now = Carbon::now();
        $basePath = storage_path("app/backups2");

        if (!File::exists($basePath)) File::makeDirectory($basePath, 0755, true);

        
        $lastBackup = DB::table('cache')
            ->where('key', 'last_backup_time2')
            ->value('value')
            ?? Carbon::now()->subYear()->toDateTimeString();

        $lastBackup = Carbon::parse($lastBackup);
        Log::info("Last backup time: ".$lastBackup);

        
        $fullFile = $basePath . "/full.sql";
        if (!File::exists($fullFile)) {
            $this->runFullBackup($host, $user, $pass, $db, $fullFile);

            DB::table('cache')->updateOrInsert(
                ['key' => 'last_backup_time2'],
                ['value' => Carbon::now()->toDateTimeString()]
            );

            Log::info("✅ Full backup completed.");
            return;
        }

        
        $dateFolder = $basePath . '/' . $now->format('Y-m-d');
        if (!File::exists($dateFolder)) 
            File::makeDirectory($dateFolder, 0755, true);

        
        $dbKey = "Tables_in_".$db;
        $tables = DB::select("SHOW TABLES");
        Log::info("Tables found: ".count($tables));
        //$tableNames = array_map(fn($t) => $t->$dbKey, $tables);
        $tableNames = ['company','companyevent','companyusers','event','userevent','zone'];


        $incremental = false;
        //$file = "{$dateFolder}/inc-" . $now->format('H-i-s') . ".sql";






        foreach ($tableNames as $table) {

            
            $cols = DB::select("SHOW COLUMNS FROM {$table}");
            $hasTimestamps = collect($cols)->contains(fn($c) =>
                in_array($c->Field, ['created_at', 'updated_at', 'deleted_at'])
            );

            if (!$hasTimestamps) {
                Log::info("⏩ Skipped {$table} — no timestamp");
                continue;
            }

            //$file = "{$dateFolder}/inc-{$table}-" . $now->format('H-i-s') . ".sql";
            Log::info("Processing table {$table}...");

            $file = "{$dateFolder}/inc-" . $now->format('H-i-s') . ".sql";

            $changed = $this->runIncrementalDump($host, $user, $pass, $db, $table, $lastBackup, $file);
            Log::info("Changed rows: ".($changed ? "YES" : "NO"));

            if ($changed) {
                $incremental = true;
                Log::info("✅ Incremental dump created for {$table}");
            } else {
                //@unlink($file);
                Log::info("ℹ️ No changes found for {$table}");
            }
        }

        if (!$incremental) {
            Log::info("ℹ️ No changed rows — incremental skipped.");

            if (File::exists($file)) {
                Log::info("Cleaning up last created file: {$file}");
                $content = File::get($file);
            if (trim($content) === '' || strpos($content, 'INSERT INTO') === false) {
                File::delete($file);
                Log::info("🧹 Deleted empty or non-data dump file: {$file}");
            }
           



            
        }
            return;
        }

        DB::table('cache')->updateOrInsert(
            ['key' => 'last_backup_time2'],
            ['value' => Carbon::now()->toDateTimeString()]
        );

        Log::info("✅ Incremental backup completed successfully.");
    }

    /* ——————————— HELPERS ——————————— */

    private function runFullBackup($host, $user, $pass, $db, $file)
    {
        $cmd = "mysqldump -h {$host} -u{$user} -p{$pass} {$db} > {$file}";
        exec($cmd, $o, $code);

        if ($code !== 0) throw new \Exception("Full mysqldump failed");
    }


    // private function runFullBackup($host, $user, $pass, $db, $file)
    // {
    
    //     if (!str_ends_with($file, '.gz')) {
    //         $file .= '.gz';
    //     }

        
    //     $cmd = "mysqldump -h {$host} -u{$user} -p{$pass} {$db}";
    //     exec($cmd, $output, $code);

    //     if ($code !== 0) {
    //         throw new \Exception("Full mysqldump failed");
    //     }

        
    //     $dumpText = implode("\n", $output);
    //     $compressed = gzencode($dumpText, 9); 

       
    //     File::put($file, $compressed);

    //     Log::info("✅ Binary-style compressed dump created: {$file}");
    // }



    private function runIncrementalDump($host, $user, $pass, $db, $table, $since, $file)
    {
        Log::info("Dumping changes since: ".$since);
        $since = Carbon::parse($since)->toDateTimeString();

        //$where = escapeshellarg("updated_at > '{$since}' OR created_at > '{$since}' OR deleted_at > '{$since}'");
        $where = "\"updated_at > '{$since}' OR created_at > '{$since}' OR deleted_at > '{$since}'\"";


        $beforeSize = File::exists($file) ? File::size($file) : 0;

        $cmd = "mysqldump -h {$host} -u{$user} -p{$pass} {$db} {$table} ".
            "--no-create-info --skip-triggers --compact ".
            "--where={$where} >> {$file}";

        Log::info("Mysqldump command: ".$cmd);


        Log::info("Executing command: ".$cmd);
        exec($cmd, $o, $code);
        Log::info("Mysqldump exit code: ".$code);

        if ($code !== 0) {
            Log::error("❌ mysqldump failed for table {$table}");
            return false;
        }

        sleep(1); // wait 1 second
        clearstatcache(true, $file); // refresh file metadata
        $afterSize = File::size($file);

        //$afterSize = File::exists($file) ? File::size($file) : 0;
        Log::info("File size before: {$beforeSize}, after: {$afterSize}");
        return $afterSize > $beforeSize;
   }


    /* ————————— FAILURE HANDLING ————————— */

    public function failed(\Throwable $exception)
    {
        $key = 'backup_job_failures';
        $fails = DB::table('cache')->where('key',$key)->value('value') ?? 0;
        $fails++;

        DB::table('cache')->updateOrInsert(['key'=>$key], ['value'=>$fails]);
        Log::error("❌ Backup failed #{$fails}: ".$exception->getMessage());

        if ($fails >= 3) {
            try {
                Mail::to("lujain.darwazeh123@gmail.com")->send(
                    new BackupFailureAlert($exception, $fails)
                );
                DB::table('cache')->where('key',$key)->delete();
            } catch (\Exception $e) {
                Log::error("Email failed: ".$e->getMessage());
            }
        }
    }


}
