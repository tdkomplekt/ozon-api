<?php

namespace Tdkomplekt\OzonApi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonCategory extends Model
{
    use HasFactory;

    protected $table = 'ozon_categories';
    protected $guarded = [];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('sortById', function (Builder $builder) {
            return $builder->orderBy('id');
        });
    }

    public function attributes()
    {
        return $this->belongsToMany(OzonAttribute::class, 'ozon_category_attribute');
    }

}
