<?php

namespace Tdkomplekt\OzonApi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tdkomplekt\OzonApi\Base\Model;

class OzonAttribute extends Model
{
    protected $table = 'ozon_attributes';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('sortById', function (Builder $builder) {
            return $builder->orderBy('id');
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(OzonCategory::class, 'ozon_category_attribute');
    }

    public function options(): BelongsToMany
    {
        $options = $this->belongsToMany(
            OzonAttributeOption::class,
            'ozon_category_attribute_option',
            'ozon_attribute_id',
            'ozon_attribute_option_id',
            'id',
            'id'
        );

        // USE CATEGORY RELATION
        if ($this->pivot && $this->pivot->ozon_category_id) {
            $options = $options->where(function ($query) {
                $query->whereIn('ozon_category_id', [0, $this->pivot->ozon_category_id]);
            });
        } elseif ($this->pivot_ozon_category_id) {
            $options = $options->where(function ($query) {
                $query->whereIn('ozon_category_id', [0, $this->pivot_ozon_category_id]);
            });
        }

        return $options;
    }

    public function values(): HasMany
    {
        $options = $this->hasMany(
            OzonProductAttributeValuePivot::class,
            'ozon_attribute_id',
            'id',
        );

        return $options;
    }
}
