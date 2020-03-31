<?php

namespace Luminary\Services\ApiQuery;

use Luminary\Database\Eloquent\Model;

class Query
{
    /**
     * Has the current query
     * been activated?
     *
     * @var bool
     */
    protected $active = false;

    /**
     * The Query Collection
     *
     * @var \Luminary\Services\ApiQuery\QueryCollection
     */
    protected $query;

    /**
     * Query constructor.
     *
     * @param \Luminary\Services\ApiQuery\QueryCollection $query
     */
    public function __construct(QueryCollection $query)
    {
        $this->query = $query;
    }

    /**
     * Get the query collection
     *
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getQuery(string $key = null, $default = null)
    {
        $collection = $this->query ?: QueryCollection::make();

        return $key ? $collection->get($key, $default) : $collection;
    }

    /**
     * Set the query collection
     *
     * @param array $query
     * @return \Luminary\Services\ApiQuery\Query
     */
    public function setQuery(array $query) :Query
    {
        $this->query = $this->query->make($query);

        return $this;
    }

    /**
     * Return whether or not the
     * query is set to active
     *
     * @return bool
     */
    public function isActive() :bool
    {
        return $this->active;
    }

    /**
     * Activate the query
     *
     * @return Query
     */
    public function activate() :Query
    {
        $this->active = true;

        Model::clearBootedModels();

        return $this;
    }

    /**
     * Deactivate the query
     *
     * @return Query
     */
    public function deactivate() :Query
    {
        $this->active = false;

        return $this;
    }

    /**
     * Return the fields array
     *
     * @param string $resource
     * @return array
     */
    public function fields(string $resource = null) :array
    {
        return $this->query->fields($resource);
    }

    /**
     * Return the filters array
     *
     * @param string $resource
     * @return array
     */
    public function filters(string $resource = null) :array
    {
        return $this->query->filters($resource);
    }

    /**
     * Return the has query
     *
     * @return array
     */
    public function has() :array
    {
        $filters = collect($this->query->hasFilters());
        $output = [];

        collect($this->query->hasQuery())
            ->each(function($has) use($filters, &$output) {
                $output[$has] = $filters->get($has);
            });

        return $output;
    }

    /**
     * Return the includes array
     *
     * @return array
     */
    public function includes() :array
    {
        return $this->query->include();
    }

    /**
     * Return the includes array
     *
     * @return array
     */
    public function with() :array
    {
        return $this->query->with();
    }

    /**
     * Return the includes array
     *
     * @return array
     */
    public function only() :array
    {
        return $this->query->queryOnly();
    }

    /**
     * Return the query pagination array
     *
     * @return array
     */
    public function pagination() :array
    {
        return $this->query->pagination();
    }

    /**
     * Return if the query is paginated
     *
     * @return bool
     */
    public function paginated() :bool
    {
        return ! empty($this->pagination());
    }

    /**
     * Return the query resource
     *
     * @return string
     */
    public function resource() :string
    {
        return $this->query->resource();
    }

    /**
     * Return the query search
     *
     * @return string
     */
    public function search() :string
    {
        return $this->query->searchStr();
    }

    /**
     * Return the query sorting array
     *
     * @param string $resource
     * @return array
     */
    public function sorting(string $resource = null) :array
    {
        return $this->query->sorting($resource);
    }

    /**
     * Return the query collection as
     * an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->query->toArray();
    }
}
