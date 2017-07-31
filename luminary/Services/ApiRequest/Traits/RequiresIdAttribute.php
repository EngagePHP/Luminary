<?php

namespace Luminary\Services\ApiRequest\Traits;

use Luminary\Services\ApiRequest\Exceptions\MissingIdAttribute;

trait RequiresIdAttribute
{
    /**
     * Check that the required type attribute exists
     *
     * @param array $data
     * @return void
     */
    public function idAttributeExists(array $data) :void
    {
        if (! array_key_exists('id', $data)) {
            $this->throwMissingIdAttributeException();
        }
    }

    /**
     * Throw a new missing data attribute exception
     *
     * @throws \Luminary\Services\ApiRequest\Exceptions\MissingTypeAttribute
     * @return void
     */
    public function throwMissingIdAttributeException() :void
    {
        $message = 'Missing the required id attribute in the data object';
        throw new MissingIdAttribute($message);
    }
}
