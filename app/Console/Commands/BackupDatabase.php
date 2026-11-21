<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'database:backup';

    protected $description = 'Dump the MySQL database to a timestamped file in storage/app/backups';

    public function handle(): int
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->warn('Backup skipped: only the mysql driver is supported.');

            return self::SUCCESS;
        }

        $config = config('database.connections.mysql');
        $dir = storage_path('app/backups');
        @mkdir($dir, 0775, true);

        $file = $dir.'/'.now()->format('Y_m_d_His').'_'.$config['database'].'.sql';

        $process = new Process(array_values(array_filter([
            str_starts_with(PHP_OS_FAMILY, 'Windows') ? 'C:/xampp/mysql/bin/mysqldump.exe' : 'mysqldump',
            '--host='.$config['host'],
            '--port='.(int) $config['port'],
            '--user='.$config['username'],
            empty($config['password']) ? null : '--password='.$config['password'],
            '--single-transaction',
            '--quick',
            $config['database'],
        ])));

        $handle = @fopen($file, 'wb');
        $process->run(function ($type, $output) use ($handle) {
            if ($handle) {
                fwrite($handle, $output);
            }
        });
        if ($handle) {
            fclose($handle);
        }

        if (! $process->isSuccessful()) {
            @unlink($file);
            $this->error('mysqldump failed: '.trim($process->getErrorOutput()));

            return self::FAILURE;
        }

        collect(glob($dir.'/*.sql'))
            ->sortByDesc(fn ($path) => filemtime($path))
            ->slice(14)
            ->each(fn ($path) => @unlink($path));

        $this->info('Backup written to '.$file);

        return self::SUCCESS;
    }
}
