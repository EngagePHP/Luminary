<?php

namespace Luminary\Services\ApiRequest\Traits;

use Luminary\Services\ApiRequest\Exceptions\MissingTypeAttribute;

trait RequiresTypeAttribute
{
    /**
     * Check that the required type attribute exists
     *
     * @param array $data
     * @return void
     */
    public function typeAttributeExists(array $data) :void
    {
        if (! array_key_exists('type', $data)) {
            $this->throwMissingTypeAttributeException();
        }
    }

    /**
     * Throw a new missing data attribute exception
     *
     * @throws \Luminary\Services\ApiRequest\Exceptions\MissingTypeAttribute
     * @return void
     */
    public function throwMissingTypeAttributeException() :void
    {
        $message = 'Missing the required type attribute for the data object';
        throw new MissingTypeAttribute($message);
    }
}
