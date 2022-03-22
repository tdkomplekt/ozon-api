<?php

namespace Tdkomplekt\OzonApi\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OzonProductAttributeValuePivot extends Pivot
{
    protected $table = 'ozon_product_attribute_values';

    public $timestamps = false;
    public $casts = [
        'values' => 'array'
    ];
}
