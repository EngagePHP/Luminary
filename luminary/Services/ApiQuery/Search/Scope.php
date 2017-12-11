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
        $terms = $this->search();

        if (empty($terms)) {
            return;
        }

        if ($this->hasSearchMethod($builder)) {
            $builder->search($terms);
        } elseif ($this->hasSearchMethod($model)) {
            $model->search($terms);
        }
    }

    /**
     * Get the query search
     *
     * @return string
     */
    protected function search()
    {
        return $this->query()->search();
    }

    /**
     * Does the provided object have a search method?
     *
     * @param object $object
     * @return bool
     */
    protected function hasSearchMethod(object $object)
    {
        return method_exists($object, 'search') || method_exists($object, 'scopeSearch');
    }
}
