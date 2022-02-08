<?php

namespace Tdkomplekt\OzonApi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OzonAttribute extends Model
{
    use HasFactory;

    protected $table = 'ozon_attributes';
    protected $guarded = [];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('sortById', function (Builder $builder) {
            return $builder->orderBy('id');
        });
    }

    public function categories()
    {
        return $this->belongsToMany(OzonCategory::class, 'ozon_category_attribute');
    }
}
