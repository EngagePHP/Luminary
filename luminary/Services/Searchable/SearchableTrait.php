<?php

namespace Luminary\Services\Searchable;

use Illuminate\Support\Facades\DB;
use Nicolaslopezj\Searchable\SearchableTrait as BaseSearchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Builder as QueryBuilder;

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

            $this->fromRaw($query, $subQuery, 'searchSub');
        });
    }

    /**
     * Create the from raw subquery
     *
     * @param QueryBuilder $query
     * @param QueryBuilder $fromQuery
     * @param string $as
     */
    protected function fromRaw(QueryBuilder $query, Builder $fromQuery, string $as)
    {
        $sql = $fromQuery->toSql();
        $bindings = $fromQuery->getBindings();
        $this->addFromQueryBinding($query);

        $query->from(new Expression('('.$sql.') as '.$as));
        $query->addBinding($bindings, 'from');
    }

    /**
     * Add from query bindings for
     * from subquery
     *
     * @param QueryBuilder $query
     */
    protected function addFromQueryBinding(QueryBuilder $query)
    {
        $bindings = $query->bindings;

        if(is_null(array_get($bindings, 'from'))) {
            $bindings['from'] = [];
            $query->bindings = $bindings;
        }
    }
}
