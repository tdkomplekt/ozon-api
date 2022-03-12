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
        $ozonApi->syncCategories();
        $ozonApi->fillCategoriesCustomFields();
        $ozonApi->syncAttributes();
        $endTime = now();

        echo "Compiled Successfully in " . $endTime->diffInSeconds($starTime) . " seconds";

        return Command::SUCCESS;
    }
}
