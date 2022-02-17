<?php

namespace Tdkomplekt\OzonApi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class OzonCategory extends Model
{
    use HasFactory;

    protected $table = 'ozon_categories';
    protected $guarded = [];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('sortBySearch', function (Builder $builder) {
            return $builder->orderBy('search');
        });
    }

    public function parent(): HasOne
    {
        return $this->hasOne(OzonCategory::class, 'id', 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(OzonCategory::class, 'parent_id', 'id');
    }

    public function attributes()
    {
        return $this->belongsToMany(OzonAttribute::class, 'ozon_category_attribute');
    }

    public function getFullTitle($separator = ' > ', $appendSelf = true): string
    {
        $parents = collect($appendSelf ? [$this] : []);
        $this->appendParentFor($parents, $this);
        return implode($separator, $parents->pluck('title')->toArray());
    }

    protected function appendParentFor(Collection &$parents, OzonCategory $category): Collection
    {
        if($category->parent) {
            $parents->prepend($category->parent);

            if ($category->parent->parent) {
                $this->appendParentFor($parents, $category->parent);
            }
        }
        return $parents;
    }

}
