<?php

namespace Tdkomplekt\OzonApi\Console\Commands;

use Illuminate\Console\Command;
use Tdkomplekt\OzonApi\OzonApi;

class SyncCategories extends Command
{
    protected $signature = 'ozon:sync-categories';

    public function handle()
    {
        $ozonApi = new OzonApi();

        $starTime = now();
        $ozonApi->syncCategories();
        $ozonApi->fillCategoriesCustomFields();
        $endTime = now();

        echo $endTime->diffForHumans($starTime);
    }
}
