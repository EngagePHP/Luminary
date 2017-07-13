<?php

namespace Luminary\Services\ApiQuery\Pagination;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\AbstractPaginator;

class Collection extends EloquentCollection
{
    /**
     * The paginator instance
     *
     * @var \Illuminate\Pagination\AbstractPaginator
     */
    protected static $paginator;

    /**
     * Create a new collection.
     *
     * @param  mixed  $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    /**
     * Get the paginator instance
     *
     * @return \Illuminate\Pagination\AbstractPaginator
     */
    public function getPaginator() :AbstractPaginator
    {
        return static::$paginator;
    }

    /**
     * Alias for getPaginator method
     *
     * @return \Illuminate\Pagination\AbstractPaginator
     */
    public function paginator()
    {
        return $this->getPaginator();
    }

    /**
     * Set the paginator instance
     *
     * @param \Illuminate\Pagination\AbstractPaginator $paginator
     * @return Collection
     */
    public function setPaginator(AbstractPaginator $paginator) :Collection
    {
        static::$paginator = $paginator;

        return $this;
    }
}
