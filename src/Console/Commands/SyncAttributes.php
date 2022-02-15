<?php

namespace Tdkomplekt\OzonApi\Console\Commands;

use Illuminate\Console\Command;
use Tdkomplekt\OzonApi\OzonApi;

class SyncAttributes extends Command
{
    protected $signature = 'ozon:sync-attributes';

    public function handle()
    {
        $ozonApi = new OzonApi();

        $starTime = now();
        $ozonApi->syncAttributes();
        $endTime = now();

        echo $endTime->diffForHumans($starTime);
    }
}
