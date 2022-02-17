<?php

namespace Tdkomplekt\OzonApi\Traits;

use Tdkomplekt\OzonApi\Models\OzonCategory;

trait HasOzonCategory
{
    public function ozonCategory()
    {
        return $this->hasOne(OzonCategory::class);
    }
}
