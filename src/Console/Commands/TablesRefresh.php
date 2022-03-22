<?php

namespace Tdkomplekt\OzonApi\Console\Commands;

use Artisan;
use DB;
use Tdkomplekt\OzonApi\Base\Command;

class TablesRefresh extends Command
{
    protected $signature = 'ozon:tables-refresh';

    public function handle()
    {
        DB::table('migrations')->where('migration', 'test')->delete();

        Artisan::call('migrate', [
            '--path' => 'vendor/tdkomplekt/ozon-api/database/migrations/2022_02_04_150000_create_ozon_tables.php'
        ]);
    }
}
