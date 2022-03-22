<?php

namespace Tdkomplekt\OzonApi\Base;

use Illuminate\Console\Command as ConsoleCommand;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Tdkomplekt\OzonApi\OzonApi;

class Command extends ConsoleCommand
{
    protected OzonApi $ozonApi;

    public function __construct()
    {
        parent::__construct();
        $this->ozonApi = app(OzonApi::class);
    }

    protected function success($endTime, $startTime): int
    {
        $this->info("Compiled Successfully in " . $endTime->diffInSeconds($startTime) . " seconds");

        return CommandAlias::SUCCESS;
    }
}
