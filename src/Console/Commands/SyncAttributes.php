<?php

namespace Tdkomplekt\OzonApi\Console\Commands;

use Illuminate\Console\Command;
use Tdkomplekt\OzonApi\OzonApi;

class SyncAttributes extends Command
{
    protected $signature = 'ozon:sync-attributes';

    public function handle()
    {
        $starTime = now();
        (new OzonApi())->syncAttributes();
        $endTime = now();
        echo $endTime->diffForHumans($starTime);
    }
}
