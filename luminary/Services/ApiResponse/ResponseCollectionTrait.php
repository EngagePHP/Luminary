<?php

namespace Luminary\Services\ApiResponse;

use Luminary\Database\Eloquent\Collection;

trait ResponseCollectionTrait
{
    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        // We want to return this empty because
        // the Json Response Serializer will handle
        // the response serialization
        return [];
    }
}
