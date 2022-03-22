<?php

namespace Tdkomplekt\OzonApi\Base;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Symfony\Component\Console\Command\Command as CommandAlias;

class Model extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $guarded = [];
    public $timestamps = false;

    protected function success($endTime, $startTime): int
    {
        $this->info("Compiled Successfully in " . $endTime->diffInSeconds($startTime) . " seconds");

        return CommandAlias::SUCCESS;
    }
}
