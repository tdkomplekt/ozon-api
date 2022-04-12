<?php

namespace Tdkomplekt\OzonApi\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tdkomplekt\OzonApi\Base\Model;

class OzonProduct extends Model
{
    use SoftDeletes;

    protected $table = 'ozon_products';
    public $timestamps = true;

    protected $casts = [
        'images' => 'array',
        'OZON_attributes' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function (OzonProduct $ozonProduct) {
            if($ozonProduct->category_id) {
                $ozonCategory = OzonCategory::find($ozonProduct->category_id);
                if($ozonCategory) {
                    $ozonProduct->attributes()->sync($ozonCategory->attributes);
                }
            }
        });

        static::deleting(function (OzonProduct $ozonProduct) {
             $ozonProduct->attributes()->detach();
        });
    }

    public function category(): HasOne
    {
        return $this->hasOne(OzonCategory::class, 'id', 'category_id');
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(OzonAttribute::class, 'ozon_product_attribute_values')
            ->using(OzonProductAttributeValuePivot::class)
            ->withPivot(['values']);
    }

    public function getOzonAttributeValuesById($attributeId)
    {
        $collection = collect($this->attributesToArray()['OZON_attributes']);
        $attributeData = $collection->firstWhere('id', $attributeId);
        return $attributeData['values'];
    }

    public function validate(): bool
    {
        return true; // TODO
    }
}
