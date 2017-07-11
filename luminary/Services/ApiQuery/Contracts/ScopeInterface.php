<?php

namespace Luminary\Services\ApiQuery\Contracts;

use Illuminate\Database\Eloquent\Model;
use Luminary\Services\ApiQuery\QueryScope;

interface ScopeInterface
{
    /**
     * ScopeInterface constructor.
     *
     * @param \Luminary\Services\ApiQuery\QueryScope $scope
     */
    public function __construct(QueryScope $scope);

    /**
     * Apply to the Query Scope
     *
     * @param $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply($builder, Model $model) :void;
}
