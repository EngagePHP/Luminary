<?php

namespace Luminary\Services\ApiQuery\Pagination;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\AbstractPaginator;
use Luminary\Services\ApiQuery\Pagination\Paginators\LengthAwarePaginator;

class Collection extends EloquentCollection
{
    /**
     * The paginator total
     *
     * @var int
     */
    public $total = 0;

    /**
     * The paginator per page
     *
     * @var int
     */
    public $perPage = 15;

    /**
     * The paginator current page
     *
     * @var int
     */
    public $currentPage = 1;

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
        $paginator = static::$paginator;

        if(!$paginator) {
            $this->setDefaultPaginator();
            return $this->paginator();
        }

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

        $this->total = $paginator->total();
        $this->currentPage = $paginator->currentPage();
        $this->perPage = $paginator->perPage();

        return $this;
    }

    /**
     * Set the paginator instance
     *
     * @return Collection
     */
    public function setDefaultPaginator() :Collection
    {
        $args = [$this->all(), $this->total, $this->perPage, $this->currentPage];
        $paginator = new LengthAwarePaginator(...$args);

        $this->setPaginator($paginator);

        return $this;
    }
}
