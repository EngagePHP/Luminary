<?php

namespace Luminary\Database\Eloquent;

use Illuminate\Database\Eloquent\Relations\Relation;

trait MorphTrait
{
    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        $morphMap = Relation::morphMap();
        $class = $this->morphClass ?: static::class;

        if (! empty($morphMap) && in_array($class, $morphMap)) {
            return array_search($class, $morphMap, true);
        }

        return $class;
    }
}
