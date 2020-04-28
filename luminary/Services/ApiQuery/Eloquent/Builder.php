<?php

namespace Luminary\Services\ApiQuery\Eloquent;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Luminary\Services\ApiQuery\Fields\BuilderTrait as FieldsBuilderTrait;
use Luminary\Services\ApiQuery\Pagination\BuilderTrait as PaginationBuilderTrait;

class Builder extends EloquentBuilder
{
    use FieldsBuilderTrait;
    use PaginationBuilderTrait;

    /**
     * Checks if a macro is registered.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasMacro($name)
    {
        return isset($this->localMacros[$name]);
    }

    /**
     * Get the current query value bindings in a flattened array.
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->query->getBindings();
    }

    /**
     * Set the table which the query is targeting.
     *
     * @param  string  $table
     * @return $this
     */
    public function from($table)
    {
        $this->query->from($table);

        return $this;
    }

    /**
     * Get the SQL representation of the query.
     *
     * @return string
     */
    public function toSql()
    {
        return $this->query->toSql();
    }
}
