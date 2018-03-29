<?php

namespace Luminary\Services\ApiQuery\Pagination;

use Illuminate\Database\Eloquent\Model;
use Luminary\Services\ApiQuery\Eloquent\BaseScope;
use Luminary\Services\ApiQuery\Query;

class Scope extends BaseScope
{
    /**
     * Apply to the Query Scope
     *
     * @param $builder
     * @param Model $model
     * @return void
     */
    public function apply($builder, Model $model) :void
    {
        if ($this->paginated()) {
            $builder->forPage(...$this->pagination());
            $builder->setPaginated(get_class($model));
        }
    }

    /**
     * Return the includes for the current
     * resource
     *
     * @return array
     */
    protected function pagination() :array
    {
        return array_values($this->query()->pagination());
    }

    /**
     * Is the current query a paginated
     * request?
     *
     * @return bool
     */
    protected function paginated() :bool
    {
        return $this->query()->paginated();
    }

    /**
     * Return the query instance
     *
     * @return Query
     */
    protected function query() :Query
    {
        return $this->scope->getQuery();
    }
}
