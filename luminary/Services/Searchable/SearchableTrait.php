<?php

namespace Luminary\Services\Searchable;

use Illuminate\Support\Facades\DB;
use Nicolaslopezj\Searchable\SearchableTrait as BaseSearchable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait SearchableTrait
 * @package Nicolaslopezj\Searchable
 * @property array $searchable
 * @property string $table
 * @property string $primaryKey
 * @method string getTable()
 */
trait SearchableTrait
{
    use BaseSearchable {
        BaseSearchable::scopeSearch as baseScopeSearch;
    }

    /**
     * Creates the search scope.
     *
     * @param \Illuminate\Database\Eloquent\Builder $q
     * @param string $search
     * @param float|null $threshold
     * @param  boolean $entireText
     * @param  boolean $entireTextOnly
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $q, $search, $threshold = null, $entireText = false, $entireTextOnly = false)
    {
        $model = $q->getModel();

        $q->whereIn('id', function($query) use($model, $search, $threshold, $entireText, $entireTextOnly) {
            $subQuery = $model->newQuery();
            $subQuery->select('id')
                ->getModel()
                ->scopeSearchRestricted($subQuery, $search, null, $threshold, $entireText, $entireTextOnly);

            $query->from($subQuery, 'searchSub');
        });
    }
}
