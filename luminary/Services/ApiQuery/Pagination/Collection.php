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
    protected $paginator;

    /**
     * Create a new collection
     *
     * @param \Illuminate\Pagination\AbstractPaginator $paginator
     */
    public function __construct(AbstractPaginator $paginator)
    {
        parent::__construct($paginator->items());

        $this->paginator = $paginator;
    }

    /**
     * Get the paginator instance
     *
     * @return \Illuminate\Pagination\AbstractPaginator
     */
    public function getPaginator() :AbstractPaginator
    {
        return $this->paginator;
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
        $this->paginator = $paginator;

        return $this;
    }
}
