<?php

namespace Luminary\Services\ApiResponse\Serializers;

use Illuminate\Support\Collection;
use Luminary\Services\ApiResponse\Presenters\ResponsePresenter;
use Luminary\Services\ApiResponse\ResponseHelper;

class CollectionSerializer extends AbstractSerializer
{
    /**
     * CollectionSerializer constructor
     *
     * @param \Illuminate\Support\Collection $data
     */
    public function __construct(Collection $data)
    {
        parent::__construct($data);

        $this->setCollection($data);
    }

    /**
     * Parse the collection and fill
     * the class attributes
     *
     * @param \Illuminate\Support\Collection $data
     */
    public function fill($data) :void
    {
        $data = $data->map(
            function ($model) {
                $model = new ModelSerializer($model);

                $this->addIncluded($model->included());

                return $model->data();
            }
        )->all();

        $this->setData($data);
        $this->setPaginatedMeta();
    }

    /**
     * Return the resource related link
     *
     * @return string
     */
    public function relatedLink() :string
    {
        return '';
    }

    /**
     * Return the resource self link
     *
     * @return string
     */
    public function selfLink() :string
    {
        return ResponseHelper::resourceSelf(null, $this->resource());
    }

    /**
     * Return the serialized array
     *
     * @return array
     */
    public function serialize() :array
    {
        return (new ResponsePresenter($this))->format();
    }
}
