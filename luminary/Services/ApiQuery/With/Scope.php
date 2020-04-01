<?php

namespace Luminary\Services\ApiQuery\With;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Luminary\Services\ApiQuery\Eloquent\BaseScope;
use Luminary\Services\ApiQuery\Helpers\RemoveQuery;

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
        $this->withQuery()->each(function($with) use($builder, $model) {
            $methodName = camel_case('with_' . $with);

            if($builder->hasMacro($methodName)) {
                $builder->{$methodName}();
                RemoveQuery::run($builder, $model, $with);
            }
        });
    }

    /**
     * Get the query search
     *
     * @return Collection
     */
    protected function withQuery()
    {
        return collect($this->query()->with());
    }
}
