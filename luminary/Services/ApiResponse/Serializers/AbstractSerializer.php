<?php

namespace Luminary\Services\ApiResponse\Serializers;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Luminary\Services\ApiQuery\Pagination\Collection as PaginatorCollection;
use Luminary\Services\ApiResponse\Presenters\LinkPresenter;
use Luminary\Services\ApiResponse\ResponseHelper;

abstract class AbstractSerializer implements SerializerInterface
{
    /**
     * The Collection data object
     *
     * @var array
     */
    protected $data = [];

    /**
     * The Collection Instance
     *
     * @var Collection
     */
    protected $collection;

    /**
     * The list of included resources
     *
     * @var array
     */
    protected $included = [];

    /**
     * Additional meta
     *
     * @var Collection
     */
    protected $meta;

    /**
     * Top Level meta
     *
     * @var Collection
     */
    protected $responseMeta;

    /**
     * Is this a paginated collection?
     *
     * @var bool
     */
    protected $paginated = false;

    /**
     * Set the relationship name property
     *
     * @var string
     */
    protected $relationship;

    /**
     * Set the resource name property
     *
     * @var string
     */
    protected $resource;

    /**
     * Set true for top level
     * serializer
     *
     * @var bool
     */
    public $topLevel = false;

    /**
     * AbstractSerializer constructor
     *
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        $this->setMeta();
        $this->setResponseMeta();

        if (!is_null($data)) {
            $this->fill($data);
        }
    }

    /**
     * Get the collection property
     *
     * @return Collection
     */
    public function collection() :Collection
    {
        return $this->collection ?: collect();
    }

    /**
     * Set the instance collection property
     * and choose to auto fill
     *
     * @param Collection $collection
     * @param bool $fill
     * @return AbstractSerializer
     */
    public function setCollection(Collection $collection, bool $fill = true) :AbstractSerializer
    {
        $this->collection = $collection;

        $this->setPaginated($this->isPaginated($collection));

        if ($fill) {
            $this->fill($collection);
        }

        return $this;
    }

    /**
     * Return the data property
     *
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Set/Replace the data property
     *
     * @param array $data
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setData(array $data) :AbstractSerializer
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Parse the data and fill
     * the class attributes
     *
     * @param mixed $data
     */
    abstract public function fill($data) :void;

    /**
     * Get the included property
     *
     * @return array
     */
    public function included() :array
    {
        return $this->included;
    }

    /**
     * Return all included relations
     *
     * @return array
     */
    public function processIncluded(): array
    {
        return collect($this->included())
            ->transform(
                function($include) {
                    return $include instanceof ModelSerializer
                        ? $include->data()
                        : $include;
                }
            )->values()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Set/Replaced the included property
     *
     * @param array $included
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setIncluded(array $included) :AbstractSerializer
    {
        $this->included = $included;

        return $this;
    }

    /**
     * And an included array to the
     * existing included property
     *
     * @param array $included
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function addIncluded(array $included) :AbstractSerializer
    {
        $included = collect($this->included())
            ->merge($included)
            ->unique()
            ->values()
            ->all();

        $this->setIncluded($included);

        return $this;
    }

    /**
     * Return the JsonApi Version
     *
     * @return string
     */
    public function jsonapi() :string
    {
        return '1.0';
    }

    /**
     * Get the links for the
     * resource collection
     *
     * @return array
     */
    public function links() :array
    {
        return (new LinkPresenter($this))->format($this->topLevel());
    }

    /**
     * Return the meta property
     * as an array
     */
    public function meta() :array
    {
        return $this->meta->toArray();
    }

    /**
     * Get/Replace the meta property
     *
     * @param array $meta
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setMeta(array $meta = null) :AbstractSerializer
    {
        $meta = $meta ?: [];

        $this->meta = collect($meta);

        return $this;
    }

    /**
     * Add an item to the meta property
     *
     * @param $key
     * @param $value
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function addMeta($key, $value) :AbstractSerializer
    {
        $this->meta->put($key, $value);

        return $this;
    }

    /**
     * Get the paginated property
     *
     * @return bool
     */
    public function paginated() :bool
    {
        return (bool) $this->paginated;
    }

    /**
     * Set the paginated property
     *
     * @param bool $bool
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setPaginated(bool $bool) :AbstractSerializer
    {
        $this->paginated = $bool;

        return $this;
    }

    /**
     * Set the paginated meta
     * from a paginator instance
     *
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setPaginatedMeta() :AbstractSerializer
    {
        $baseUrl = $this->selfLink();

        if ($this->paginated()) {
            $paginator = $this->paginator();
            $meta = collect($paginator->toArray())->map(function ($item, $key) use ($baseUrl) {
                if (preg_match('/_url/i', $key)) {
                    return $baseUrl . $item;
                }

                 return $item;
            })->toArray();

            $this->addResponseMeta('pagination', $meta);
        }

        return $this;
    }

    /**
     * Create an array of pagination links
     * if the Serializer is paginated
     *
     * @return array
     */
    public function paginationLinks() :array
    {
        $paginator = $this->paginator();
        $baseUrl = $this->selfLink();

        $urls = [
            'first' => $paginator->firstPageUrl(),
            'last' => $paginator->lastPageUrl(),
            'prev' => $paginator->previousPageUrl(),
            'next'=> $paginator->nextPageUrl()
        ];

        return collect($urls)->map(
            function ($query) use ($baseUrl) {
                return $query ? $baseUrl . $query : null;
            }
        )->all();
    }

    /**
     * Get the paginator instance
     *
     * @return \Illuminate\Pagination\AbstractPaginator
     */
    protected function paginator() :AbstractPaginator
    {
        return $this->collection()->getPaginator();
    }

    /**
     * Check if a collection is an instance
     * of PaginatorCollection
     *
     * @param Collection $collection
     * @return bool
     */
    public function isPaginated(Collection $collection)
    {
        return (bool) ($collection instanceof PaginatorCollection);
    }

    /**
     * Create an array of pagination links
     * if the Serializer is paginated
     *
     * @return string
     */
    abstract public function relatedLink() :string;

    /**
     * Get the resource property
     *
     * @return string
     */
    public function resource() :string
    {
        return (string) $this->resource ?: ResponseHelper::resource();
    }

    /**
     * Set the resource property
     *
     * @param null $resource
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setResource($resource = null) :AbstractSerializer
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get the resource property
     *
     * @return string
     */
    public function relationship() :string
    {
        return (string) $this->resource ?: ResponseHelper::resource();
    }

    /**
     * Set the resource property
     *
     * @param null $relationship
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setRelationship($relationship = null) :AbstractSerializer
    {
        $this->relationship = $relationship;

        return $this;
    }

    /**
     * Return the resource self link
     *
     * @return string
     */
    abstract public function selfLink() :string;

    /**
     * Return the serialized array
     *
     * @return array|string|null
     */
    abstract public function serialize();

    /**
     * Return the responseMeta property
     * as an array
     */
    public function responseMeta() :array
    {
        return $this->responseMeta->toArray();
    }

    /**
     * Get/Replace the responseMeta property
     *
     * @param array $meta
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function setResponseMeta(array $meta = null) :AbstractSerializer
    {
        $meta = $meta ?: [];

        $this->responseMeta = collect($meta);

        return $this;
    }

    /**
     * Add an item to the responseMeta property
     *
     * @param $key
     * @param $value
     * @return \Luminary\Services\ApiResponse\Serializers\AbstractSerializer
     */
    public function addResponseMeta($key, $value) :AbstractSerializer
    {
        $this->responseMeta->put($key, $value);

        return $this;
    }

    /**
     * Get the top level property
     *
     * @return bool
     */
    public function topLevel()
    {
        return $this->topLevel;
    }

    /**
     * Set whether the serializer is the top level
     *
     * @param bool $bool
     * @return $this
     */
    public function setTopLevel($bool = true)
    {
        $this->topLevel = $bool;

        return $this;
    }
}
