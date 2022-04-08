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

    public function saveAttributeValuesArray($attributeId, array $values)
    {
        $attribute = $this->attributes()->get()->where('id', $attributeId)->first();

        $ozonProductValue = $attribute->pivot;

        if($attribute && $ozonProductValue) {
            $ozonProductValue->values = $values;
            $ozonProductValue->save();
        }
    }

//    public function updateAttributeValueById(int $attributeId, string $value = null)
//    {
//        $attribute = $this->attributes()->get()->where('id', $attributeId)->first();
//
//        if($attribute->dictionary_id) {
//            $option = $this->getAttributeOptionByValue($attribute, $value);
//
//            $this->saveAttributeValuesArray($attribute->id, [[
//                'option_id' => $option->id,
//                'value' => $option->value,
//            ]]);
//        } else {
//            $this->saveAttributeValuesArray($attribute->id, [[
//                'value' => $value,
//            ]]);
//        }
//    }

//    protected function getAttributeOptionByValue($attribute, $value): ?OzonAttributeOption
//    {
//        $option = null;
//        $options = $attribute->options()
//            ->whereIn('ozon_category_id', [0, $this->category_id])
//            ->where('value', $value)
//            ->get();
//
//        if (count($options) == 1) {
//            $option = $options->first();
//        }
//
//        return $option;
//    }

//    public function updateAttributeValueByName(string $attributeName, string $value = null)
//    {
//        $attribute = $this->attributes()->get()->where('name', $attributeName)->first();
//        if($attribute) {
//            $this->updateAttributeValueById($attribute->id, $value);
//        }
//    }

//    public function getAllAttributesArrayList(): array
//    {
//        return $this->attributes()->get()->toArray();
//    }
//
//    public function getRequiredAttributesArrayList(): array
//    {
//        return $this->attributes()->get()->where('is_required', 1)->toArray();
//    }
//
//    public function getFilledAttributesArrayList(): array
//    {
//        $results = [];
//        foreach ($this->attributes()->get() as $attribute) {
//            $results[$attribute->name] = $attribute->pivot->values;
//        }
//        return $results;
//    }

    public function validate(): bool
    {
        return true; // TODO
    }
}
