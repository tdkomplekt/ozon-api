<?php

namespace Tdkomplekt\OzonApi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tdkomplekt\OzonApi\Base\Model;

class OzonProduct extends Model
{
    protected $table = 'ozon_products';
    public $timestamps = true;

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

    public function saveAttributeValuesArray($attributeId, array $values)
    {
        $attribute = $this->attributes()->get()->where('id', $attributeId)->first();

        $ozonProductValue = $attribute->pivot;

        if($attribute && $ozonProductValue) {
            $ozonProductValue->values = $values;
            $ozonProductValue->save();
        }
    }

    public function updateAttributeValue($attributeIdOrName, string $value)
    {
        if (is_integer($attributeIdOrName)) {
            $attribute = $this->attributes()->get()->where('id', $attributeIdOrName)->first();
        } else {
            $attribute = $this->attributes()->get()->where('name', $attributeIdOrName)->first();
        }

        if($attribute->dictionary_id) {
            $options = $attribute->options()
                ->whereIn('ozon_category_id', [0, $this->category_id])
                ->where('value', $value)
                ->get();

            if (count($options) == 1) {
                $option = $options->first();

                $this->saveAttributeValuesArray($attribute->id, [
                    'option_id' => $option->id,
                    'value' => $option->value,
                ]);
            }
        } else {
            $this->saveAttributeValuesArray($attribute->id, [
                'value' => $value,
            ]);
        }
    }

    public function getRequiredAttributesArrayList(): array
    {
        return $this->attributes()->get()->where('is_required', 1)->toArray();
    }

    public function validate(): bool
    {
        return true; // TODO
    }

}
