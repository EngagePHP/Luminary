<?php

namespace Luminary\Services\ApiQuery\Pagination;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Luminary\Services\ApiQuery\Pagination\Paginators\LengthAwarePaginator;
use Luminary\Services\ApiQuery\Query;

trait BuilderTrait
{
    /**
     * Is the instance paginated
     *
     * @var bool
     */
    protected static $paginated;

    /**
     * @var null|\Luminary\Services\ApiQuery\Pagination\Collection
     */
    protected static $paginatedCollection;

    /**
     * Return and instance of paginator if paginate scope query
     * is paginated
     *
     * @param  array $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get($columns = array('*'))
    {
        $collection = parent::get($columns);

        if($this->isPaginated($this->getModel())) {
            $collection = $this->paginateCollection($collection);
            static::$paginatedCollection = $collection;
        }

        return $collection;
    }

    /**
     * Get the paginated var
     *
     * @return bool
     */
    public static function getPaginated() :bool
    {
        return static::$paginated;
    }

    /**
     * Set the paginated property
     *
     * @param string $class
     * @return void
     */
    public static function setPaginated(string $class) :void
    {
        static::$paginated = $class;
    }

    /**
     * Is the current model paginated
     *
     * @param Model $model
     * @return bool
     */
    public static function isPaginated(Model $model) :bool
    {
        return static::$paginated ? $model instanceof static::$paginated : false;
    }

    /**
     * Get the stored paginated collection
     *
     * @return mixed
     */
    public static function getPaginatedCollection()
    {
        return static::$paginatedCollection;
    }

    /**
     * Paginate a collection of models
     * and return a paginator instance
     *
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @return \Luminary\Services\ApiQuery\Pagination\Collection
     */
    public function paginateCollection(EloquentCollection $collection) :Collection
    {
        $params = $this->getPaginationParams();
        $paginator = $this->paginator(
            $collection,
            $this->getPaginationCount(),
            $params->get('per_page'),
            $params->get('page'),
            $this->getPaginationOptions()
        );

        return (new Collection($paginator->items()))->setPaginator($paginator);
    }

    /**
     * The the pagination parameters
     * from the Query
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPaginationParams() :SupportCollection
    {
        return collect(app(Query::class)->pagination());
    }

    /**
     * Return the pagination options
     *
     * @return array
     */
    public function getPaginationOptions() :array
    {
        $query = app(Query::class)->getQuery()->except(['resource'])->toArray();

        return compact('query');
    }

    /**
     * Get the pagination count
     *
     * @return int
     */
    public function getPaginationCount() :int
    {
        $query = $this->applyScopes()->getQuery();
        return $query->getCountForPagination();
    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param  \Illuminate\Support\Collection  $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  array  $options
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function paginator($items, $total, $perPage, $currentPage, $options)
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items',
            'total',
            'perPage',
            'currentPage',
            'options'
        ));
    }
}
