<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseMonitor extends Command
{
    protected $signature = 'db:monitor {--timeout=60}';
    protected $description = 'Monitor database connection';

    public function handle()
    {
        $timeout = $this->option('timeout');
        $start = time();

        while (time() - $start < $timeout) {
            try {
                DB::connection()->getPdo();
                return 0;
            } catch (\Exception $e) {
                sleep(1);
            }
        }

        return 1;
    }
} 