<?php

namespace Tdkomplekt\OzonApi\Console\Commands;

use Illuminate\Console\Command;
use Tdkomplekt\OzonApi\OzonApi;

class SyncAll extends Command
{
    protected $signature = 'ozon:sync-all';

    public function handle()
    {
        $ozonApi = new OzonApi();

        $starTime = now();

//        $ozonApi->syncCategories();
        $ozonApi->syncAttributes();

        $endTime = now();

        echo $endTime->diffForHumans($starTime);
    }
}
