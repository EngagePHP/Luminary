<?php

namespace Luminary\Services\ApiQuery\Only;

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
        $this->onlyQuery()->each(function($only) use($builder, $model) {
            $methodName = camel_case('only_' . $only);
            if($builder->hasMacro($methodName)) {
                $model->{$methodName}();
                RemoveQuery::run($builder, $model, $only);
            }
        });
    }

    /**
     * Get the query search
     *
     * @return Collection
     */
    protected function onlyQuery()
    {
        return collect($this->query()->only());
    }
}
