<?php

namespace Luminary\Services\ApiQuery\Search;

use Illuminate\Database\Eloquent\Model;
use Luminary\Services\ApiQuery\Eloquent\BaseScope;

class Scope extends BaseScope
{
    /**
     * Query parser paginate
     *
     * @var array
     */
    protected $search;

    /**
     * Apply to the Query Scope
     *
     * @param $builder
     * @param Model $model
     * @return void
     */
    public function apply($builder, Model $model) :void
    {
        $terms = $this->searchQuery();

        if (empty($terms)) {
            return;
        }

        if ($method = $this->getSearchMethod($builder)) {
            $method == 'scopeSearch'
                ? $builder->{$method}($builder, $terms)
                : $builder->{$method}($terms);
        } elseif ($method = $this->getSearchMethod($model)) {
            $method == 'scopeSearch'
                ? $model->{$method}($builder, $terms)
                : $model->{$method}($terms);
        }
    }

    /**
     * Get the query search
     *
     * @return string
     */
    protected function searchQuery()
    {
        return $this->query()->search();
    }

    /**
     * Get the builder/model search method
     *
     * @param $object
     * @return null|string
     */
    protected function getSearchMethod($object)
    {
        if (method_exists($object, 'search')) {
            return 'search';
        } elseif (method_exists($object, 'scopeSearch')) {
            return 'scopeSearch';
        }

        return null;
    }
}
