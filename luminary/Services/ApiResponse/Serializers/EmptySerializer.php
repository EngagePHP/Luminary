<?php

namespace Luminary\Services\ApiResponse\Serializers;

use Luminary\Services\ApiResponse\Presenters\ResponsePresenter;
use Luminary\Services\ApiResponse\ResponseHelper;

class EmptySerializer extends AbstractSerializer
{
    /**
     * ArraySerializer constructor.
     */
    public function __construct()
    {
        parent::__construct([]);
    }

    /**
     * Parse the collection and fill
     * the class attributes
     *
     * @param array $data
     */
    public function fill($data) :void
    {
        // N/A
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
        return '';
    }

    /**
     * Return a formatted array for
     * Json Serialization
     *
     * @return null
     */
    public function serialize()
    {
        return null;
    }
}
