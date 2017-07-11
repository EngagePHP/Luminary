<?php

namespace Luminary\Services\ApiQuery\Eloquent;

use Luminary\Services\ApiQuery\Contracts\ScopeInterface;
use Luminary\Services\ApiQuery\Query;
use Luminary\Services\ApiQuery\QueryScope;

abstract class BaseScope implements ScopeInterface
{
    /**
     * The QueryScope instance
     *
     * @var \Luminary\Services\ApiQuery\QueryScope
     */
    protected $scope;

    /**
     * The eloquent model instance
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The eloquent builder instance
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
     * ScopeInterface constructor.
     *
     * @param \Luminary\Services\ApiQuery\QueryScope $scope
     */
    public function __construct(QueryScope $scope)
    {
        $this->scope = $scope;
    }

    /**
     * Return the query instance
     *
     * @return \Luminary\Services\ApiQuery\Query
     */
    protected function query() :Query
    {
        return $this->scope->getQuery();
    }

    /**
     * Return the query resource
     *
     * @return string
     */
    protected function resource() :string
    {
        return $this->scope->resource();
    }

    /**
     * Get the model table name
     *
     * @return string
     */
    protected function table() :string
    {
        return $this->model->getTable();
    }
}
