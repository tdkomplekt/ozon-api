<?php

namespace Tdkomplekt\OzonApi\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tdkomplekt\OzonApi\Base\Model;

class OzonProduct extends Model
{
    use SoftDeletes;

    protected $table = 'ozon_products';
    public $timestamps = true;

    protected $casts = [
        'attributes' => 'array',
        'complex_attributes' => 'array',
        'images' => 'array',
        'images360' => 'array',
        'pdf_list' => 'array',
    ];

    public function category(): HasOne
    {
        return $this->hasOne(OzonCategory::class, 'id', 'category_id');
    }

    public function getOzonAttributeValuesById($attributeId)
    {
        $collection = collect($this->attributesToArray()['attributes']);
        $attributeData = $collection->firstWhere('id', $attributeId);
        return $attributeData['values'];
    }

    public function validate(): bool
    {
        return true; // TODO
    }
}
