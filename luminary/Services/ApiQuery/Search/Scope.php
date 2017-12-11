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

        if (method_exists($builder, 'search')) {
            $builder->search($terms);
        } elseif (method_exists($model, 'search')) {
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
}
