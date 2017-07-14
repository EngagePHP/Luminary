<?php

namespace Luminary\Services\ApiQuery\Fields;

trait BuilderTrait
{
    /**
     * Create a collection of models from plain arrays.
     * Include hidden and visible attributes from
     * the parent model
     *
     * @param  array  $items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function hydrate(array $items)
    {
        return parent::hydrate($items)->map(
            function ($model) {
                $base = method_exists($this, 'getRelated') ? $this->getRelated() : $this->getModel();
                $model->setHidden($base->getHidden());
                return $model;
            }
        );
    }
}
