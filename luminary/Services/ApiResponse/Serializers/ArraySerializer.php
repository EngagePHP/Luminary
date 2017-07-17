<?php

namespace Luminary\Services\ApiResponse\Serializers;

use Luminary\Services\ApiResponse\Presenters\ResponsePresenter;
use Luminary\Services\ApiResponse\ResponseHelper;

class ArraySerializer extends AbstractSerializer
{
    /**
     * ArraySerializer constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    /**
     * Parse the collection and fill
     * the class attributes
     *
     * @param array $data
     */
    public function fill($data) :void
    {
        $this->setData($data);
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
     * Return a formatted array for
     * Json Serialization
     *
     * @return array
     */
    public function serialize() :array
    {
        return (new ResponsePresenter($this))->format();
    }
}
